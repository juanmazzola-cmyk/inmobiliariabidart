<?php

namespace App\Livewire;

use App\Models\Configuracion as ConfiguracionModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Configuración')]
class Configuracion extends Component
{
    use WithFileUploads;

    public string $razonSocial  = '';
    public string $cuit         = '';
    public string $direccion    = '';
    public string $telefono     = '';
    public string $email        = '';
    public ?string $logoActual  = null;
    public $nuevoLogo           = null;
    public $archivoBackup       = null;

    protected function rules(): array
    {
        return [
            'email'         => 'nullable|email',
            'nuevoLogo'     => 'nullable|image|max:2048',
            'archivoBackup' => 'nullable|file|max:51200',
        ];
    }

    protected $messages = [
        'email.email'            => 'El email no tiene formato válido.',
        'nuevoLogo.image'        => 'El logo debe ser una imagen.',
        'nuevoLogo.max'          => 'El logo no puede superar 2 MB.',
        'archivoBackup.file'     => 'El archivo no es válido.',
        'archivoBackup.max'      => 'El archivo no puede superar 50 MB.',
    ];

    public function mount(): void
    {
        $config = ConfiguracionModel::get();
        $this->razonSocial = $config->razon_social ?? '';
        $this->cuit        = $config->cuit ?? '';
        $this->direccion   = $config->direccion ?? '';
        $this->telefono    = $config->telefono ?? '';
        $this->email       = $config->email ?? '';
        $this->logoActual  = $config->logo_path;
    }

    public function guardar(): void
    {
        $this->validate();

        $config = ConfiguracionModel::get();

        $logoPath = $config->logo_path;

        if ($this->nuevoLogo) {
            // Eliminar logo anterior
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $this->nuevoLogo->storeAs('config', 'logo.' . $this->nuevoLogo->getClientOriginalExtension(), 'public');
            $this->logoActual = $logoPath;
            $this->nuevoLogo  = null;
        }

        $config->update([
            'razon_social'=> $this->razonSocial ?: null,
            'cuit'        => $this->cuit ?: null,
            'direccion'   => $this->direccion ?: null,
            'telefono'    => $this->telefono ?: null,
            'email'       => $this->email ?: null,
            'logo_path'   => $logoPath,
        ]);

        session()->flash('success', 'Configuración guardada correctamente.');
    }

    public function eliminarLogo(): void
    {
        $config = ConfiguracionModel::get();
        if ($config->logo_path) {
            Storage::disk('public')->delete($config->logo_path);
            $config->update(['logo_path' => null]);
            $this->logoActual = null;
        }
    }

    public function importarBackup(): void
    {
        $this->validate(['archivoBackup' => 'required|file|max:51200']);

        $ext = strtolower($this->archivoBackup->getClientOriginalExtension());
        if (!in_array($ext, ['sql', 'txt'])) {
            $this->addError('archivoBackup', 'El archivo debe tener extensión .sql o .txt');
            return;
        }

        $sql = file_get_contents($this->archivoBackup->getRealPath());
        $statements = $this->parseSql($sql);

        DB::beginTransaction();
        try {
            foreach ($statements as $stmt) {
                DB::unprepared($stmt);
            }
            DB::commit();
            $this->archivoBackup = null;
            session()->flash('success', 'Backup importado correctamente. La base de datos fue restaurada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Error al importar el backup: ' . $e->getMessage());
        }
    }

    private function parseSql(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $strChar    = '';
        $i          = 0;
        $len        = strlen($sql);

        while ($i < $len) {
            $c = $sql[$i];

            if ($inString) {
                $current .= $c;
                if ($c === '\\') {
                    $i++;
                    if ($i < $len) $current .= $sql[$i];
                } elseif ($c === $strChar) {
                    $inString = false;
                }
            } else {
                if ($c === "'" || $c === '"') {
                    $inString = true;
                    $strChar  = $c;
                    $current .= $c;
                } elseif ($c === '-' && ($i + 1 < $len) && $sql[$i + 1] === '-') {
                    while ($i < $len && $sql[$i] !== "\n") $i++;
                    $i++;
                    continue;
                } elseif ($c === '/' && ($i + 1 < $len) && $sql[$i + 1] === '*') {
                    $i += 2;
                    while ($i < $len && !($sql[$i] === '*' && ($i + 1 < $len) && $sql[$i + 1] === '/')) $i++;
                    $i += 2;
                    continue;
                } elseif ($c === ';') {
                    $stmt = trim($current);
                    if ($stmt !== '') $statements[] = $stmt;
                    $current = '';
                } else {
                    $current .= $c;
                }
            }
            $i++;
        }

        $stmt = trim($current);
        if ($stmt !== '') $statements[] = $stmt;

        return $statements;
    }

    public function render()
    {
        return view('livewire.configuracion');
    }
}

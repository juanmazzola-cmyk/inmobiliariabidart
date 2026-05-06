<?php

namespace App\Livewire;

use App\Models\CategoriaGasto;
use App\Models\Configuracion as ConfiguracionModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    public string $razonSocial    = '';
    public string $cuit           = '';
    public string $direccion      = '';
    public string $telefono       = '';
    public string $email          = '';
    public ?string $logoActual    = null;
    public $nuevoLogo             = null;
    public string $loginUsuario      = '';
    public string $loginPassword     = '';
    public string $loginWhatsapp     = '';

    public string $nuevaCategoria    = '';

    protected function rules(): array
    {
        return [
            'email'         => 'nullable|email',
            'nuevoLogo'     => 'nullable|image|max:2048',
            'loginUsuario'  => 'required|min:3',
            'loginPassword' => 'nullable|min:4',
            'loginWhatsapp' => 'nullable',
        ];
    }

    protected $messages = [
        'email.email'            => 'El email no tiene formato válido.',
        'nuevoLogo.image'        => 'El logo debe ser una imagen.',
        'nuevoLogo.max'          => 'El logo no puede superar 2 MB.',
        'loginUsuario.required'  => 'El usuario es obligatorio.',
        'loginUsuario.min'       => 'El usuario debe tener al menos 3 caracteres.',
        'loginPassword.min'      => 'La contraseña debe tener al menos 4 caracteres.',
    ];

    public function mount(): void
    {
        $config = ConfiguracionModel::get();
        $this->razonSocial    = $config->razon_social ?? '';
        $this->cuit           = $config->cuit ?? '';
        $this->direccion      = $config->direccion ?? '';
        $this->telefono       = $config->telefono ?? '';
        $this->email          = $config->email ?? '';
        $this->logoActual     = $config->logo_path;
        $this->loginUsuario   = $config->login_usuario ?? 'admin';
        $this->loginWhatsapp  = $config->login_whatsapp ?? '';
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
            'razon_social'   => $this->razonSocial ?: null,
            'cuit'           => $this->cuit ?: null,
            'direccion'      => $this->direccion ?: null,
            'telefono'       => $this->telefono ?: null,
            'email'          => $this->email ?: null,
            'logo_path'      => $logoPath,
            'login_usuario'  => $this->loginUsuario,
            'login_whatsapp' => $this->loginWhatsapp ?: null,
        ]);

        // Actualizar usuario del sistema
        $user = User::first();
        if ($user) {
            $userData = ['name' => $this->loginUsuario];
            if ($this->loginPassword) {
                $userData['password'] = Hash::make($this->loginPassword);
                $this->loginPassword  = '';
            }
            $user->update($userData);
        }

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

    public function agregarCategoria(): void
    {
        $this->validate(['nuevaCategoria' => 'required|min:2|unique:categorias_gastos,nombre'], [
            'nuevaCategoria.required' => 'Ingresá un nombre.',
            'nuevaCategoria.min'      => 'Mínimo 2 caracteres.',
            'nuevaCategoria.unique'   => 'Esa categoría ya existe.',
        ]);

        CategoriaGasto::create(['nombre' => mb_convert_case(trim($this->nuevaCategoria), MB_CASE_TITLE)]);
        $this->nuevaCategoria = '';
    }

    public function eliminarCategoria(int $id): void
    {
        CategoriaGasto::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.configuracion', [
            'categorias' => CategoriaGasto::orderBy('nombre')->get(),
        ]);
    }
}

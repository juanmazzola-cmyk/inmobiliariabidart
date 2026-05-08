<?php

namespace App\Livewire;

use App\Models\Propiedad;
use App\Models\Propietario;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Propiedades en alquiler')]
class Propiedades extends Component
{
    use WithPagination, WithFileUploads;

    #[Url]
    public string $busqueda = '';
    #[Url]
    public string $filtroEstado = '';
    #[Url]
    public string $filtroTipo = '';
    #[Url]
    public string $filtroPropietario = '';
    #[Url]
    public ?int $abrirConPropietario = null;

    public bool $modalAbrir = false;
    public ?int $propiedadId = null;

    public ?int $propietarioId       = null;
    public string $tipo              = 'casa';
    public string $direccion         = '';
    public string $numero            = '';
    public string $piso              = '';
    public string $departamentoLetra = '';
    public string $ciudad            = '';
    public string $provincia         = 'Buenos Aires';
    public string $codigoPostal      = '';
    public string $superficieTotal   = '';
    public string $superficieCubierta = '';
    public string $ambientes         = '';
    public string $banios            = '';
    public bool $cochera             = false;
    public string $descripcion       = '';
    public string $valorReferencia   = '';
    public string $estado            = 'disponible';

    public array $fotosActuales  = [];
    public array $fotosEliminar  = [];
    public $nuevasFotos          = [];

    protected function rules(): array
    {
        return [
            'direccion'     => 'required|min:1',
            'ciudad'        => 'required|min:2',
            'tipo'          => 'required',
            'estado'        => 'required|in:disponible,alquilada,en_reparacion,inactiva',
            'nuevasFotos'   => 'nullable|array|max:10',
            'nuevasFotos.*' => 'nullable|image|max:3072',
        ];
    }

    protected $messages = [
        'direccion.required'   => 'La dirección es obligatoria.',
        'direccion.min'        => 'La dirección debe tener al menos 3 caracteres.',
        'ciudad.required'      => 'La ciudad es obligatoria.',
        'ciudad.min'           => 'La ciudad debe tener al menos 2 caracteres.',
        'nuevasFotos.*.image'  => 'Cada foto debe ser una imagen.',
        'nuevasFotos.*.max'    => 'Cada foto no puede superar 3 MB.',
    ];

    public function mount(): void
    {
        if ($this->abrirConPropietario) {
            $this->nueva();
            $this->propietarioId = $this->abrirConPropietario;
        }
    }

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedValorReferencia(): void
    {
        $raw = str_replace(['.', ',', ' '], '', $this->valorReferencia);
        if ($raw !== '' && is_numeric($raw)) {
            $this->valorReferencia = number_format((int) $raw, 0, ',', '.');
        }
    }

    public function nueva(): void
    {
        $this->resetForm();
        $this->modalAbrir = true;
    }

    public function editar(int $id): void
    {
        $p = Propiedad::findOrFail($id);
        $this->propiedadId        = $id;
        $this->propietarioId      = $p->propietario_id;
        $this->tipo               = $p->tipo;
        $this->direccion          = $p->direccion;
        $this->numero             = $p->numero ?? '';
        $this->piso               = $p->piso ?? '';
        $this->departamentoLetra  = $p->departamento_letra ?? '';
        $this->ciudad             = $p->ciudad;
        $this->provincia          = $p->provincia;
        $this->codigoPostal       = $p->codigo_postal ?? '';
        $this->superficieTotal    = $p->superficie_total ?? '';
        $this->superficieCubierta = $p->superficie_cubierta ?? '';
        $this->ambientes          = $p->ambientes ?? '';
        $this->banios             = $p->banios ?? '';
        $this->cochera            = $p->cochera;
        $this->descripcion        = $p->descripcion ?? '';
        $this->valorReferencia    = $p->valor_referencia
            ? number_format($p->valor_referencia, 0, ',', '.')
            : '';
        $this->estado             = $p->estado;
        $this->fotosActuales      = $p->fotos ?? [];
        $this->fotosEliminar      = [];
        $this->nuevasFotos        = [];
        $this->modalAbrir         = true;
    }

    public function marcarEliminarFoto(int $index): void
    {
        $this->fotosEliminar[] = $this->fotosActuales[$index];
        array_splice($this->fotosActuales, $index, 1);
    }

    public function guardar(): void
    {
        $this->validate();

        foreach ($this->fotosEliminar as $path) {
            Storage::disk('public')->delete($path);
        }

        $fotos = $this->fotosActuales;
        foreach ($this->nuevasFotos as $foto) {
            $fotos[] = $foto->store('propiedades-alquiler', 'public');
        }

        $datos = [
            'propietario_id'      => $this->propietarioId ?: null,
            'tipo'                => $this->tipo,
            'direccion'           => $this->direccion,
            'numero'              => $this->numero ?: null,
            'piso'                => $this->piso ?: null,
            'departamento_letra'  => $this->departamentoLetra ?: null,
            'ciudad'              => $this->ciudad,
            'provincia'           => $this->provincia,
            'codigo_postal'       => $this->codigoPostal ?: null,
            'superficie_total'    => $this->superficieTotal ?: null,
            'superficie_cubierta' => $this->superficieCubierta ?: null,
            'ambientes'           => $this->ambientes ?: null,
            'banios'              => $this->banios ?: null,
            'cochera'             => $this->cochera,
            'descripcion'         => $this->descripcion ?: null,
            'valor_referencia'    => $this->valorReferencia !== ''
                ? str_replace(['.', ' '], '', $this->valorReferencia)
                : null,
            'fotos'               => count($fotos) > 0 ? $fotos : null,
            'estado'              => $this->estado,
        ];

        if ($this->propiedadId) {
            Propiedad::findOrFail($this->propiedadId)->update($datos);
            $msg = 'Propiedad actualizada correctamente.';
        } else {
            Propiedad::create($datos);
            $msg = 'Propiedad cargada correctamente.';
        }

        $this->cerrarModal();
        session()->flash('success', $msg);
    }

    public function eliminar(int $id): void
    {
        $p = Propiedad::findOrFail($id);
        foreach ($p->fotos ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        $p->delete();
        session()->flash('success', 'Propiedad eliminada.');
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->resetValidation();
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->propiedadId        = null;
        $this->propietarioId      = null;
        $this->tipo               = 'casa';
        $this->direccion          = '';
        $this->numero             = '';
        $this->piso               = '';
        $this->departamentoLetra  = '';
        $this->ciudad             = '';
        $this->provincia          = 'Buenos Aires';
        $this->codigoPostal       = '';
        $this->superficieTotal    = '';
        $this->superficieCubierta = '';
        $this->ambientes          = '';
        $this->banios             = '';
        $this->cochera            = false;
        $this->descripcion        = '';
        $this->valorReferencia    = '';
        $this->fotosActuales      = [];
        $this->fotosEliminar      = [];
        $this->nuevasFotos        = [];
        $this->estado             = 'disponible';
    }

    public function render()
    {
        $propiedades = Propiedad::with(['propietario'])
            ->withCount(['contratos'])
            ->when($this->busqueda, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('direccion', 'like', "%{$this->busqueda}%")
                        ->orWhere('ciudad', 'like', "%{$this->busqueda}%")
                        ->orWhereHas('propietario', fn($p) =>
                            $p->where('apellido', 'like', "%{$this->busqueda}%")
                              ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                        )
                        ->orWhereHas('contratos.inquilino', fn($i) =>
                            $i->where('apellido', 'like', "%{$this->busqueda}%")
                              ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                        )
                )
            )
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroPropietario, fn($q) => $q->where('propietario_id', $this->filtroPropietario))
            ->orderBy('ciudad')
            ->orderBy('direccion')
            ->paginate(15);

        return view('livewire.propiedades', [
            'propiedades'  => $propiedades,
            'propietarios' => Propietario::where('activo', true)->orderBy('apellido')->get(),
        ]);
    }
}

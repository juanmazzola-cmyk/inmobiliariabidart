<?php

namespace App\Livewire;

use App\Models\Propietario;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Propietarios')]
class Propietarios extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';
    public bool $modalAbrir = false;
    public ?int $propietarioId = null;

    public string $nombre = '';
    public string $apellido = '';
    public string $dni = '';
    public string $cuit = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';
    public string $ciudad = '';
    public string $provincia = '';
    public string $cbu = '';
    public string $banco = '';
    public bool $activo = true;
    public string $observaciones = '';

    protected $rules = [
        'nombre' => 'required|min:2',
        'apellido' => 'required|min:2',
        'dni' => 'required|min:7',
        'email' => 'nullable|email',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'apellido.required' => 'El apellido es obligatorio.',
        'dni.required' => 'El DNI es obligatorio.',
        'email.email' => 'El email no tiene formato válido.',
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $propietarios = Propietario::withCount(['propiedades', 'propiedadesVenta'])
            ->when($this->busqueda, fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('apellido', 'like', "%{$this->busqueda}%")
                  ->orWhere('dni', 'like', "%{$this->busqueda}%")
                  ->orWhere('email', 'like', "%{$this->busqueda}%")
            )
            ->orderBy('apellido')
            ->paginate(15);

        return view('livewire.propietarios', compact('propietarios'));
    }

    public function nuevo(): void
    {
        $this->reset(['propietarioId', 'nombre', 'apellido', 'dni', 'cuit', 'telefono', 'email', 'direccion', 'ciudad', 'provincia', 'cbu', 'banco', 'observaciones']);
        $this->activo = true;
        $this->modalAbrir = true;
    }

    public function editar(int $id): void
    {
        $p = Propietario::findOrFail($id);
        $this->propietarioId = $id;
        $this->nombre = $p->nombre;
        $this->apellido = $p->apellido;
        $this->dni = $p->dni;
        $this->cuit = $p->cuit ?? '';
        $this->telefono = $p->telefono ?? '';
        $this->email = $p->email ?? '';
        $this->direccion = $p->direccion ?? '';
        $this->ciudad = $p->ciudad ?? '';
        $this->provincia = $p->provincia ?? '';
        $this->cbu = $p->cbu ?? '';
        $this->banco = $p->banco ?? '';
        $this->activo = $p->activo;
        $this->observaciones = $p->observaciones ?? '';
        $this->modalAbrir = true;
    }

    public function guardar(): void
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'cuit' => $this->cuit ?: null,
            'telefono' => $this->telefono ?: null,
            'email' => $this->email ?: null,
            'direccion' => $this->direccion ?: null,
            'ciudad' => $this->ciudad ?: null,
            'provincia' => $this->provincia ?: null,
            'cbu' => $this->cbu ?: null,
            'banco' => $this->banco ?: null,
            'activo' => $this->activo,
            'observaciones' => $this->observaciones ?: null,
        ];

        if ($this->propietarioId) {
            Propietario::findOrFail($this->propietarioId)->update($data);
            $mensaje = 'Propietario actualizado correctamente.';
        } else {
            Propietario::create($data);
            $mensaje = 'Propietario creado correctamente.';
        }

        $this->cerrarModal();
        session()->flash('success', $mensaje);
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->resetValidation();
        $this->reset(['propietarioId', 'nombre', 'apellido', 'dni', 'cuit', 'telefono', 'email', 'direccion', 'ciudad', 'provincia', 'cbu', 'banco', 'observaciones']);
        $this->activo = true;
    }

    public function toggleActivo(int $id): void
    {
        $p = Propietario::findOrFail($id);
        $p->update(['activo' => !$p->activo]);
    }
}

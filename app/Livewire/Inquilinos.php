<?php

namespace App\Livewire;

use App\Models\Inquilino;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Inquilinos')]
class Inquilinos extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';
    public bool $modalAbrir = false;
    public ?int $inquilinoId = null;

    public string $nombre = '';
    public string $apellido = '';
    public string $dni = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';
    public string $ciudad = '';
    public string $provincia = '';
    public string $ocupacion = '';
    public string $garanteNombre = '';
    public string $garanteDni = '';
    public string $garanteTelefono = '';
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
        $inquilinos = Inquilino::withCount(['contratos'])
            ->when($this->busqueda, fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('apellido', 'like', "%{$this->busqueda}%")
                  ->orWhere('dni', 'like', "%{$this->busqueda}%")
                  ->orWhere('email', 'like', "%{$this->busqueda}%")
            )
            ->orderBy('apellido')
            ->paginate(15);

        return view('livewire.inquilinos', compact('inquilinos'));
    }

    public function nuevo(): void
    {
        $this->reset(['inquilinoId', 'nombre', 'apellido', 'dni', 'telefono', 'email', 'direccion', 'ciudad', 'provincia', 'ocupacion', 'garanteNombre', 'garanteDni', 'garanteTelefono', 'observaciones']);
        $this->activo = true;
        $this->modalAbrir = true;
    }

    public function editar(int $id): void
    {
        $i = Inquilino::findOrFail($id);
        $this->inquilinoId = $id;
        $this->nombre = $i->nombre;
        $this->apellido = $i->apellido;
        $this->dni = $i->dni;
        $this->telefono = $i->telefono ?? '';
        $this->email = $i->email ?? '';
        $this->direccion = $i->direccion ?? '';
        $this->ciudad = $i->ciudad ?? '';
        $this->provincia = $i->provincia ?? '';
        $this->ocupacion = $i->ocupacion ?? '';
        $this->garanteNombre = $i->garante_nombre ?? '';
        $this->garanteDni = $i->garante_dni ?? '';
        $this->garanteTelefono = $i->garante_telefono ?? '';
        $this->activo = $i->activo;
        $this->observaciones = $i->observaciones ?? '';
        $this->modalAbrir = true;
    }

    public function guardar(): void
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'telefono' => $this->telefono ?: null,
            'email' => $this->email ?: null,
            'direccion' => $this->direccion ?: null,
            'ciudad' => $this->ciudad ?: null,
            'provincia' => $this->provincia ?: null,
            'ocupacion' => $this->ocupacion ?: null,
            'garante_nombre' => $this->garanteNombre ?: null,
            'garante_dni' => $this->garanteDni ?: null,
            'garante_telefono' => $this->garanteTelefono ?: null,
            'activo' => $this->activo,
            'observaciones' => $this->observaciones ?: null,
        ];

        if ($this->inquilinoId) {
            Inquilino::findOrFail($this->inquilinoId)->update($data);
            $msg = 'Inquilino actualizado correctamente.';
        } else {
            Inquilino::create($data);
            $msg = 'Inquilino creado correctamente.';
        }

        $this->cerrarModal();
        session()->flash('success', $msg);
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->resetValidation();
        $this->reset(['inquilinoId', 'nombre', 'apellido', 'dni', 'telefono', 'email', 'direccion', 'ciudad', 'provincia', 'ocupacion', 'garanteNombre', 'garanteDni', 'garanteTelefono', 'observaciones']);
        $this->activo = true;
    }
}

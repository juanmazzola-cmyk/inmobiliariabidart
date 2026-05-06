<?php

namespace App\Livewire;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\Propiedad;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Gastos')]
class Gastos extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';
    #[Url]
    public string $filtroCategoria = '';
    public ?int $filtroPropiedad = null;

    public bool $modalAbrir = false;
    public ?int $gastoId = null;

    public ?int $propiedadId = null;
    public string $concepto = '';
    public string $categoria = 'reparacion';
    public string $monto = '';
    public string $fecha = '';
    public string $proveedor = '';
    public string $comprobante = '';
    public bool $deducible = true;
    public string $observaciones = '';

    protected $rules = [
        'propiedadId' => 'required',
        'concepto' => 'required|min:3',
        'categoria' => 'required',
        'monto' => 'required|numeric|min:1',
        'fecha' => 'required|date',
    ];

    public function mount(): void
    {
        $this->fecha = now()->format('Y-m-d');
    }

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $gastos = Gasto::with(['propiedad.propietario'])
            ->when($this->busqueda, fn($q) =>
                $q->where('concepto', 'like', "%{$this->busqueda}%")
                  ->orWhere('proveedor', 'like', "%{$this->busqueda}%")
            )
            ->when($this->filtroCategoria, fn($q) => $q->where('categoria', $this->filtroCategoria))
            ->when($this->filtroPropiedad, fn($q) => $q->where('propiedad_id', $this->filtroPropiedad))
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('livewire.gastos', [
            'gastos'      => $gastos,
            'propiedades' => Propiedad::with('propietario')->orderBy('ciudad')->get(),
            'categorias'  => CategoriaGasto::orderBy('nombre')->pluck('nombre'),
            'totalGastos' => Gasto::when($this->filtroCategoria, fn($q) => $q->where('categoria', $this->filtroCategoria))
                ->when($this->filtroPropiedad, fn($q) => $q->where('propiedad_id', $this->filtroPropiedad))
                ->sum('monto'),
        ]);
    }

    public function nuevo(): void
    {
        $this->reset(['gastoId', 'propiedadId', 'concepto', 'proveedor', 'comprobante', 'observaciones', 'monto']);
        $this->categoria = CategoriaGasto::orderBy('nombre')->value('nombre') ?? '';
        $this->deducible = true;
        $this->fecha = now()->format('Y-m-d');
        $this->modalAbrir = true;
    }

    public function editar(int $id): void
    {
        $g = Gasto::findOrFail($id);
        $this->gastoId = $id;
        $this->propiedadId = $g->propiedad_id;
        $this->concepto = $g->concepto;
        $this->categoria = $g->categoria;
        $this->monto = $g->monto;
        $this->fecha = $g->fecha->format('Y-m-d');
        $this->proveedor = $g->proveedor ?? '';
        $this->comprobante = $g->comprobante ?? '';
        $this->deducible = $g->deducible;
        $this->observaciones = $g->observaciones ?? '';
        $this->modalAbrir = true;
    }

    public function guardar(): void
    {
        $this->validate();

        $data = [
            'propiedad_id' => $this->propiedadId,
            'concepto' => $this->concepto,
            'categoria' => $this->categoria,
            'monto' => (float) $this->monto,
            'fecha' => $this->fecha,
            'proveedor' => $this->proveedor ?: null,
            'comprobante' => $this->comprobante ?: null,
            'deducible' => $this->deducible,
            'observaciones' => $this->observaciones ?: null,
        ];

        if ($this->gastoId) {
            Gasto::findOrFail($this->gastoId)->update($data);
            $msg = 'Gasto actualizado correctamente.';
        } else {
            Gasto::create($data);
            $msg = 'Gasto registrado correctamente.';
        }

        $this->cerrarModal();
        session()->flash('success', $msg);
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->resetValidation();
    }
}

<?php

namespace App\Livewire;

use App\Models\Gasto;
use App\Models\Pago;
use App\Models\Contrato;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pagos')]
class Pagos extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';
    #[Url]
    public string $filtroEstado = '';
    #[Url]
    public int $filtroMes = 0;
    #[Url]
    public int $filtroAnio = 0;
    #[Url]
    public ?int $filtroContrato = null;

    public bool $modalAbrir = false;
    public ?int $pagoId = null;
    public array $gastosDisponibles = [];
    public array $gastosAplicadosIds = [];

    public ?int $contratoId = null;
    public string $fechaPago = '';
    public string $fechaVencimiento = '';
    public int $periodoMes = 0;
    public int $periodoAnio = 0;
    public string $monto = '';
    public string $recargo = '0';
    public string $descuento = '0';
    public string $descuentoCategoria = '';
    public string $medioPago = 'efectivo';
    public string $numeroComprobante = '';
    public string $estado = 'pagado';
    public string $observaciones = '';

    protected $rules = [
        'contratoId'       => 'required',
        'fechaVencimiento' => 'required|date',
        'periodoMes'       => 'required|integer|between:1,12',
        'periodoAnio'      => 'required|integer|min:2020',
        'monto'            => 'required|numeric|min:1',
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->filtroAnio      = now()->year;
        $this->periodoMes      = now()->month;
        $this->periodoAnio     = now()->year;
        $this->fechaVencimiento = now()->format('Y-m-d');
        $this->fechaPago       = now()->format('Y-m-d');
    }

    public function render()
    {
        $pagos = Pago::with(['contrato.propiedad.propietario', 'contrato.inquilino'])
            ->when($this->busqueda, fn($q) =>
                $q->whereHas('contrato.inquilino', fn($i) =>
                    $i->where('apellido', 'like', "%{$this->busqueda}%")
                      ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                )
            )
            ->when($this->filtroEstado,   fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroMes,      fn($q) => $q->where('periodo_mes', $this->filtroMes))
            ->when($this->filtroAnio,     fn($q) => $q->where('periodo_anio', $this->filtroAnio))
            ->when($this->filtroContrato, fn($q) => $q->where('contrato_id', $this->filtroContrato))
            ->orderByRaw("FIELD(estado, 'vencido', 'pendiente', 'pagado', 'parcial')")
            ->orderByDesc('periodo_anio')
            ->orderByDesc('periodo_mes')
            ->paginate(20);

        $totalCobrado = Pago::where('estado', 'pagado')
            ->when($this->filtroMes,  fn($q) => $q->where('periodo_mes', $this->filtroMes))
            ->when($this->filtroAnio, fn($q) => $q->where('periodo_anio', $this->filtroAnio))
            ->sum('total');

        $totalPendiente = Pago::where('estado', 'pendiente')
            ->when($this->filtroMes,  fn($q) => $q->where('periodo_mes', $this->filtroMes))
            ->when($this->filtroAnio, fn($q) => $q->where('periodo_anio', $this->filtroAnio))
            ->sum('total');

        $totalVencido = Pago::where('estado', 'vencido')
            ->when($this->filtroMes,  fn($q) => $q->where('periodo_mes', $this->filtroMes))
            ->when($this->filtroAnio, fn($q) => $q->where('periodo_anio', $this->filtroAnio))
            ->sum('total');

        return view('livewire.pagos', [
            'pagos'          => $pagos,
            'totalCobrado'   => $totalCobrado,
            'totalPendiente' => $totalPendiente,
            'totalVencido'   => $totalVencido,
            'contratos'      => Contrato::with(['propiedad', 'inquilino'])
                ->where(fn($q) => $q->where('estado', 'activo')
                    ->orWhere('id', $this->contratoId)
                )->get(),
            'categorias'     => \App\Models\CategoriaGasto::orderBy('nombre')->pluck('nombre'),
        ]);
    }

    public function nuevoPago(): void
    {
        $this->pagoId          = null;
        $this->contratoId      = null;
        $this->monto           = '';
        $this->recargo             = '0';
        $this->descuento           = '0';
        $this->descuentoCategoria  = '';
        $this->medioPago           = 'efectivo';
        $this->estado          = 'pagado';
        $this->numeroComprobante = '';
        $this->observaciones   = '';
        $this->periodoMes      = now()->month;
        $this->periodoAnio     = now()->year;
        $this->fechaPago       = now()->format('Y-m-d');
        $this->fechaVencimiento = now()->format('Y-m-d');
        $this->gastosAplicadosIds = [];
        $this->resetValidation();
        $this->modalAbrir      = true;
    }

    public function abrirModalCobro(int $id): void
    {
        $pago = Pago::with(['contrato'])->findOrFail($id);
        $this->pagoId           = $id;
        $this->contratoId       = $pago->contrato_id;
        $this->periodoMes       = $pago->periodo_mes;
        $this->periodoAnio      = $pago->periodo_anio;
        $this->fechaVencimiento = $pago->fecha_vencimiento->format('Y-m-d');
        $this->fechaPago        = now()->format('Y-m-d');
        $this->monto            = $pago->monto;
        $this->recargo          = $pago->recargo ?? '0';
        $this->descuento        = '0';
        $this->descuentoCategoria = '';
        $this->medioPago        = 'efectivo';
        $this->estado           = 'pagado';
        $this->numeroComprobante = '';
        $this->observaciones    = $pago->observaciones ?? '';
        $this->gastosAplicadosIds = [];
        $this->resetValidation();

        // Cargar gastos y auto-aplicar todos los disponibles del inmueble
        $this->cargarGastos();
        if (!empty($this->gastosDisponibles)) {
            $todos = collect($this->gastosDisponibles);
            $this->descuento          = (string)(int)$todos->sum('monto');
            $categorias               = $todos->pluck('categoria')->unique();
            $this->descuentoCategoria = $categorias->count() === 1 ? $categorias->first() : '';
            $this->gastosAplicadosIds = $todos->pluck('id')->toArray();
            $this->estado             = 'pagado';
        }

        $this->modalAbrir = true;
    }

    public function editarPago(int $id): void
    {
        $pago = Pago::with(['contrato'])->findOrFail($id);
        $this->pagoId            = $id;
        $this->contratoId        = $pago->contrato_id;
        $this->periodoMes        = $pago->periodo_mes;
        $this->periodoAnio       = $pago->periodo_anio;
        $this->fechaVencimiento  = $pago->fecha_vencimiento->format('Y-m-d');
        $this->fechaPago         = $pago->fecha_pago ? $pago->fecha_pago->format('Y-m-d') : now()->format('Y-m-d');
        $this->monto             = $pago->monto;
        $this->recargo           = $pago->recargo ?? '0';
        $this->descuento         = $pago->descuento ?? '0';
        $this->descuentoCategoria = $pago->descuento_categoria ?? '';
        $this->medioPago         = $pago->medio_pago;
        $this->numeroComprobante = $pago->numero_comprobante ?? '';
        $this->estado            = $pago->estado;
        $this->observaciones     = $pago->observaciones ?? '';
        $this->resetValidation();
        $this->modalAbrir        = true;
    }

    public function updatedContratoId(): void
    {
        if ($this->contratoId) {
            $contrato = Contrato::find($this->contratoId);
            if ($contrato) {
                $this->monto = $contrato->monto_alquiler;
                $this->cargarGastos();
            }
        } else {
            $this->gastosDisponibles = [];
        }
    }

    public function updatedPeriodoMes(): void { $this->cargarGastos(); }
    public function updatedPeriodoAnio(): void { $this->cargarGastos(); }

    private function cargarGastos(): void
    {
        if (!$this->contratoId) return;
        $contrato = Contrato::find($this->contratoId);
        if (!$contrato) return;

        $this->gastosDisponibles = Gasto::where('propiedad_id', $contrato->propiedad_id)
            ->whereNull('pago_id')
            ->where(fn($q) =>
                $q->whereMonth('fecha', $this->periodoMes)
                  ->whereYear('fecha', $this->periodoAnio)
                  ->orWhere(fn($q2) =>
                      $q2->whereNull('liquidacion_id')
                         ->where('fecha', '>=', now()->subMonths(3))
                  )
            )
            ->orderByDesc('fecha')
            ->get()
            ->map(fn($g) => [
                'id'       => $g->id,
                'concepto' => $g->concepto,
                'categoria'=> $g->categoria,
                'monto'    => (float) $g->monto,
                'mes'      => $g->fecha->month,
                'anio'     => $g->fecha->year,
            ])
            ->toArray();
    }

    public function aplicarGasto(int $gastoId): void
    {
        $gasto = Gasto::find($gastoId);
        if (!$gasto) return;
        $this->descuento          = (string) (int) $gasto->monto;
        $this->descuentoCategoria = $gasto->categoria;
        $this->gastosAplicadosIds = [$gastoId];
    }

    public function guardar(): void
    {
        $this->validate();

        $monto    = (float) $this->monto;
        $recargo  = (float) $this->recargo;
        $descuento = (float) $this->descuento;
        $total    = $monto + $recargo - $descuento;

        $datos = [
            'contrato_id'       => $this->contratoId,
            'fecha_pago'        => $this->estado === 'pagado' ? $this->fechaPago : null,
            'fecha_vencimiento' => $this->fechaVencimiento,
            'periodo_mes'       => $this->periodoMes,
            'periodo_anio'      => $this->periodoAnio,
            'monto'             => $monto,
            'recargo'           => $recargo,
            'descuento'           => $descuento,
            'descuento_categoria' => $descuento > 0 ? ($this->descuentoCategoria ?: null) : null,
            'total'               => $total,
            'medio_pago'        => $this->medioPago,
            'numero_comprobante'=> $this->numeroComprobante ?: null,
            'estado'            => $this->estado,
            'observaciones'     => $this->observaciones ?: null,
        ];

        if ($this->pagoId) {
            // Desligar gastos previos de este pago antes de re-vincular
            Gasto::where('pago_id', $this->pagoId)->update(['pago_id' => null]);
            Pago::findOrFail($this->pagoId)->update($datos);
            $pagoIdGuardado = $this->pagoId;
            $msg = 'Pago registrado correctamente.';
        } else {
            $pagoCreado = Pago::create($datos);
            $pagoIdGuardado = $pagoCreado->id;
            $msg = 'Pago creado correctamente.';
        }

        // Marcar los gastos aplicados como descontados en este pago
        if (!empty($this->gastosAplicadosIds) && $descuento > 0) {
            Gasto::whereIn('id', $this->gastosAplicadosIds)->update(['pago_id' => $pagoIdGuardado]);
        }

        $this->cerrarModal();
        session()->flash('success', $msg);
    }

    public function generarRecibo(int $id)
    {
        $pago = Pago::with([
            'contrato.propiedad.propietario',
            'contrato.inquilino',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.recibo', compact('pago'))
            ->setPaper('a4', 'portrait');

        $nombre = 'recibo_' . str_pad($pago->id, 6, '0', STR_PAD_LEFT)
            . '_' . $pago->periodo_mes . '_' . $pago->periodo_anio . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $nombre,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir         = false;
        $this->gastosDisponibles  = [];
        $this->gastosAplicadosIds = [];
        $this->resetValidation();
    }
}

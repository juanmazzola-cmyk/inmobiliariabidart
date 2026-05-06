<?php

namespace App\Livewire;

use App\Models\Contrato;
use App\Models\Gasto;
use App\Models\Liquidacion;
use App\Models\Propietario;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Liquidaciones')]
class Liquidaciones extends Component
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

    // Modal estado (editar existente)
    public bool $modalAbrir = false;
    public ?int $liquidacionId = null;
    public string $estado = '';
    public ?string $fechaPagoPropietario = null;
    public string $medioPago = 'transferencia';
    public string $observaciones = '';

    // Modal nueva liquidación
    public bool $modalNueva = false;
    public ?int $nuevaContratoId = null;
    public int $nuevaMes = 0;
    public int $nuevaAnio = 0;
    public string $nuevaMontoAlquiler      = '';
    public string $nuevaComisionPorcentaje = '';
    public string $nuevaDescuentoTipo      = 'porcentaje';
    public string $nuevaDescuentoValorFijo = '';
    public string $nuevaDescuentoMoneda    = 'ARS';
    public string $nuevaTotalGastos        = '0';
    public string $nuevaObservaciones      = '';

    // Gastos de la liquidación
    public array  $nuevosGastos    = [];
    public string $gastoCategoria  = '';
    public string $gastoMonto      = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $liquidaciones = Liquidacion::with(['propietario', 'propiedad', 'contrato.inquilino'])
            ->when($this->busqueda, fn($q) =>
                $q->whereHas('propietario', fn($p) =>
                    $p->where('nombre', 'like', "%{$this->busqueda}%")
                      ->orWhere('apellido', 'like', "%{$this->busqueda}%")
                )->orWhereHas('propiedad', fn($p) =>
                    $p->where('direccion', 'like', "%{$this->busqueda}%")
                )
            )
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroMes,    fn($q) => $q->where('periodo_mes', $this->filtroMes))
            ->when($this->filtroAnio,   fn($q) => $q->where('periodo_anio', $this->filtroAnio))
            ->orderByDesc('periodo_anio')
            ->orderByDesc('periodo_mes')
            ->paginate(15);

        // Calcular monto neto preview para modal nueva
        $nuevaMontoComision = 0;
        $nuevaMontoNeto     = 0;
        if ($this->modalNueva && $this->nuevaMontoAlquiler) {
            $alquiler = (float) $this->nuevaMontoAlquiler;
            $gastos   = (float) $this->nuevaTotalGastos;
            if ($this->nuevaDescuentoTipo === 'valor') {
                $nuevaMontoComision = (float) $this->nuevaDescuentoValorFijo;
            } else {
                $nuevaMontoComision = round($alquiler * (float) $this->nuevaComisionPorcentaje / 100, 2);
            }
            $nuevaMontoNeto = round($alquiler - $nuevaMontoComision - $gastos, 2);
        }

        return view('livewire.liquidaciones', [
            'liquidaciones'      => $liquidaciones,
            'totalPendientes'    => Liquidacion::where('estado', 'emitida')->count(),
            'totalPagadas'       => Liquidacion::where('estado', 'pagada')->count(),
            'montoPendiente'     => Liquidacion::where('estado', 'emitida')->sum('monto_neto'),
            'contratosActivos'   => Contrato::with(['propiedad.propietario', 'inquilino'])
                                        ->where('estado', 'activo')
                                        ->orderBy('id')
                                        ->get(),
            'nuevaMontoComision' => $nuevaMontoComision,
            'nuevaMontoNeto'     => $nuevaMontoNeto,
            'categorias'         => \App\Models\CategoriaGasto::orderBy('nombre')->pluck('nombre'),
        ]);
    }

    // ── Gastos de la liquidación ────────────────────────────────────────────

    public function agregarGasto(): void
    {
        if (!$this->gastoCategoria || !$this->gastoMonto) return;
        $monto = (float) str_replace('.', '', $this->gastoMonto);
        if ($monto <= 0) return;
        $this->nuevosGastos[] = ['categoria' => $this->gastoCategoria, 'monto' => $monto];
        $this->gastoCategoria = '';
        $this->gastoMonto     = '';
        $this->nuevaTotalGastos = (string) collect($this->nuevosGastos)->sum('monto');
    }

    public function quitarGasto(int $index): void
    {
        array_splice($this->nuevosGastos, $index, 1);
        $this->nuevaTotalGastos = (string) collect($this->nuevosGastos)->sum('monto');
    }

    public function updatedGastoMonto(): void
    {
        $raw = preg_replace('/[^\d]/', '', $this->gastoMonto);
        $this->gastoMonto = $raw ? number_format((int) $raw, 0, ',', '.') : '';
    }

    // ── Modal nueva liquidación ─────────────────────────────────────────────

    public function abrirNueva(): void
    {
        $this->nuevaContratoId        = null;
        $this->nuevaMes               = now()->month;
        $this->nuevaAnio              = now()->year;
        $this->nuevaMontoAlquiler      = '';
        $this->nuevaComisionPorcentaje = '';
        $this->nuevaDescuentoTipo      = 'porcentaje';
        $this->nuevaDescuentoValorFijo = '';
        $this->nuevaDescuentoMoneda    = 'ARS';
        $this->nuevaTotalGastos        = '0';
        $this->nuevaObservaciones      = '';
        $this->resetValidation();
        $this->modalNueva = true;
    }

    public function updatedNuevaContratoId(): void
    {
        if ($this->nuevaContratoId) {
            $c = Contrato::find($this->nuevaContratoId);
            if ($c) {
                $this->nuevaMontoAlquiler      = $c->monto_alquiler;
                $this->nuevaComisionPorcentaje = $c->comision_porcentaje;
            }
        }
    }

    public function guardarNueva(): void
    {
        $rules = [
            'nuevaContratoId'    => 'required|integer',
            'nuevaMes'           => 'required|integer|between:1,12',
            'nuevaAnio'          => 'required|integer|min:2020',
            'nuevaMontoAlquiler' => 'required|numeric|min:1',
            'nuevaTotalGastos'   => 'nullable|numeric|min:0',
        ];
        if ($this->nuevaDescuentoTipo === 'porcentaje') {
            $rules['nuevaComisionPorcentaje'] = 'required|numeric|between:0,100';
        } else {
            $rules['nuevaDescuentoValorFijo'] = 'required|numeric|min:0';
        }
        $this->validate($rules, [
            'nuevaContratoId.required'       => 'Seleccioná un contrato.',
            'nuevaMontoAlquiler.required'     => 'El monto es obligatorio.',
            'nuevaDescuentoValorFijo.required'=> 'Ingresá el monto del descuento.',
        ]);

        $contrato    = Contrato::with('propiedad')->findOrFail($this->nuevaContratoId);
        $alquiler    = (float) $this->nuevaMontoAlquiler;
        $totalGastos = (float) $this->nuevaTotalGastos;

        if ($this->nuevaDescuentoTipo === 'valor') {
            $montoComision = (float) $this->nuevaDescuentoValorFijo;
            $comisionPct   = $alquiler > 0 ? round($montoComision / $alquiler * 100, 2) : 0;
        } else {
            $comisionPct   = (float) $this->nuevaComisionPorcentaje;
            $montoComision = round($alquiler * $comisionPct / 100, 2);
        }
        $montoNeto = round($alquiler - $montoComision - $totalGastos, 2);

        // Verificar unicidad
        $existe = Liquidacion::where('contrato_id', $this->nuevaContratoId)
            ->where('periodo_mes', $this->nuevaMes)
            ->where('periodo_anio', $this->nuevaAnio)
            ->exists();

        if ($existe) {
            $this->addError('nuevaContratoId', 'Ya existe una liquidación para este contrato y período.');
            return;
        }

        $liquidacion = Liquidacion::create([
            'propietario_id'      => $contrato->propiedad->propietario_id,
            'propiedad_id'        => $contrato->propiedad_id,
            'contrato_id'         => $this->nuevaContratoId,
            'periodo_mes'         => $this->nuevaMes,
            'periodo_anio'        => $this->nuevaAnio,
            'fecha_liquidacion'   => now()->toDateString(),
            'monto_alquiler'      => $alquiler,
            'comision_porcentaje' => $comisionPct,
            'descuento_tipo'      => $this->nuevaDescuentoTipo,
            'monto_comision'      => $montoComision,
            'total_gastos'        => $totalGastos,
            'monto_neto'          => $montoNeto,
            'estado'              => 'emitida',
            'observaciones'       => $this->nuevaObservaciones ?: null,
        ]);

        // Crear registros de Gasto para cada ítem ingresado
        foreach ($this->nuevosGastos as $g) {
            Gasto::create([
                'propiedad_id'   => $contrato->propiedad_id,
                'liquidacion_id' => $liquidacion->id,
                'categoria'      => $g['categoria'],
                'concepto'       => $g['categoria'],
                'monto'          => $g['monto'],
                'fecha'          => now()->startOfMonth()->setMonth($this->nuevaMes)->setYear($this->nuevaAnio)->toDateString(),
                'deducible'      => true,
            ]);
        }

        $this->modalNueva = false;
        session()->flash('success', 'Liquidación creada correctamente.');
    }

    public function cerrarModalNueva(): void
    {
        $this->modalNueva    = false;
        $this->nuevosGastos  = [];
        $this->gastoCategoria = '';
        $this->gastoMonto    = '';
        $this->nuevaTotalGastos = '0';
        $this->resetValidation();
    }

    // ── Modal cambiar estado ────────────────────────────────────────────────

    public function abrirModal(int $id): void
    {
        $liq = Liquidacion::findOrFail($id);
        $this->liquidacionId        = $id;
        $this->estado               = $liq->estado;
        $this->fechaPagoPropietario = $liq->fecha_pago_propietario?->format('Y-m-d');
        $this->medioPago            = $liq->medio_pago ?? 'transferencia';
        $this->observaciones        = $liq->observaciones ?? '';
        $this->modalAbrir           = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->reset(['liquidacionId', 'estado', 'fechaPagoPropietario', 'medioPago', 'observaciones']);
    }

    public function cambiarEstado(): void
    {
        $liq = Liquidacion::findOrFail($this->liquidacionId);
        $liq->update([
            'estado'                 => $this->estado,
            'fecha_pago_propietario' => $this->estado === 'pagada'
                ? ($this->fechaPagoPropietario ?? now()->toDateString())
                : null,
            'medio_pago'             => $this->estado === 'pagada' ? $this->medioPago : null,
            'observaciones'          => $this->observaciones ?: null,
        ]);
        $this->cerrarModal();
        session()->flash('success', 'Liquidación actualizada correctamente.');
    }


    // ── PDF ─────────────────────────────────────────────────────────────────

    public function generarPdf(int $id)
    {
        $liquidacion = Liquidacion::with([
            'propietario',
            'propiedad',
            'contrato.inquilino',
        ])->findOrFail($id);

        $gastos = $liquidacion->gastos()->get();
        if ($gastos->isEmpty()) {
            $gastos = Gasto::where('propiedad_id', $liquidacion->propiedad_id)
                ->whereMonth('fecha', $liquidacion->periodo_mes)
                ->whereYear('fecha', $liquidacion->periodo_anio)
                ->get();
        }
        // Si sigue vacío pero hay total_gastos, buscar sin filtro de fecha
        if ($gastos->isEmpty() && $liquidacion->total_gastos > 0) {
            $gastos = Gasto::where('propiedad_id', $liquidacion->propiedad_id)
                ->whereNull('liquidacion_id')
                ->orderByDesc('fecha')
                ->get();
        }

        $config = \App\Models\Configuracion::get();
        $pdf = Pdf::loadView('pdf.liquidacion', compact('liquidacion', 'gastos', 'config'))
            ->setPaper('a4', 'portrait');

        $nombre = 'liquidacion_' . str_pad($liquidacion->id, 6, '0', STR_PAD_LEFT)
            . '_' . $liquidacion->periodo_mes . '_' . $liquidacion->periodo_anio . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $nombre,
            ['Content-Type' => 'application/pdf']
        );
    }
}

<?php

namespace App\Livewire;

use App\Models\Contrato;
use App\Models\Gasto;
use App\Models\Liquidacion;
use App\Models\Pago;
use App\Models\Propiedad;
use App\Models\PropiedadVenta;
use App\Models\Propietario;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Reportes')]
class Reportes extends Component
{
    #[Url]
    public int $anio = 0;
    #[Url]
    public int $mes = 0;
    #[Url]
    public ?int $propietarioId = null;

    public function mount(): void
    {
        if (!$this->anio) {
            $this->anio = now()->year;
        }
    }

    public function render()
    {
        // ── 1. Cobranza mensual ────────────────────────────────────────────
        $cobranzaMensual = collect(range(1, 12))->map(function ($m) {
            $cobrado   = Pago::where('periodo_anio', $this->anio)->where('periodo_mes', $m)->where('estado', 'pagado')->sum('total');
            $pendiente = Pago::where('periodo_anio', $this->anio)->where('periodo_mes', $m)->whereIn('estado', ['pendiente', 'vencido'])->sum('total');
            return [
                'mes'       => $m,
                'nombre'    => ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$m],
                'cobrado'   => $cobrado,
                'pendiente' => $pendiente,
            ];
        });

        $totalAnio     = $cobranzaMensual->sum('cobrado');
        $pendienteAnio = $cobranzaMensual->sum('pendiente');

        // ── 2. Cuotas pendientes/vencidas ──────────────────────────────────
        $cuotasPendientes = Pago::with(['contrato.inquilino', 'contrato.propiedad'])
            ->whereIn('estado', ['pendiente', 'vencido'])
            ->when($this->mes,  fn($q) => $q->where('periodo_mes', $this->mes))
            ->when($this->anio, fn($q) => $q->where('periodo_anio', $this->anio))
            ->orderByRaw("FIELD(estado,'vencido','pendiente')")
            ->orderBy('fecha_vencimiento')
            ->get();

        // ── 3. Liquidaciones por período (detalle individual) ──────────────
        $liquidacionesDetalle = Liquidacion::with(['propietario', 'propiedad'])
            ->where('periodo_anio', $this->anio)
            ->when($this->mes,          fn($q) => $q->where('periodo_mes', $this->mes))
            ->when($this->propietarioId, fn($q) => $q->where('propietario_id', $this->propietarioId))
            ->orderByDesc('periodo_anio')
            ->orderByDesc('periodo_mes')
            ->orderBy('propietario_id')
            ->get();

        $liquidacionesResumen = [
            'pagadas'   => $liquidacionesDetalle->where('estado', 'pagada')->sum('monto_neto'),
            'pendientes'=> $liquidacionesDetalle->whereIn('estado', ['borrador', 'emitida'])->sum('monto_neto'),
            'cantidad_pend' => $liquidacionesDetalle->whereIn('estado', ['borrador', 'emitida'])->count(),
        ];

        // ── 4. Agrupado por propietario (para el resumen) ──────────────────
        $propietariosMap = Propietario::pluck('id')->mapWithKeys(fn($id) => [$id => Propietario::find($id)?->nombre_completo]);

        $liquidacionesPorPropietario = Liquidacion::query()
            ->where('periodo_anio', $this->anio)
            ->when($this->mes, fn($q) => $q->where('periodo_mes', $this->mes))
            ->selectRaw('propietario_id,
                SUM(monto_alquiler) as total_alquiler,
                SUM(monto_comision) as total_comision,
                SUM(total_gastos)   as total_gastos,
                SUM(monto_neto)     as total_neto,
                COUNT(*) as cantidad')
            ->groupBy('propietario_id')
            ->get()
            ->map(fn($row) => [
                'propietario'    => $propietariosMap[$row->propietario_id] ?? 'N/A',
                'total_alquiler' => $row->total_alquiler,
                'total_comision' => $row->total_comision,
                'total_gastos'   => $row->total_gastos,
                'total_neto'     => $row->total_neto,
                'cantidad'       => $row->cantidad,
            ]);

        // ── 5. Gastos por categoría ────────────────────────────────────────
        $gastosPorCategoria = Gasto::where('deducible', true)
            ->whereYear('fecha', $this->anio)
            ->when($this->mes, fn($q) => $q->whereMonth('fecha', $this->mes))
            ->selectRaw('categoria, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        // ── 6. Propiedades disponibles alquiler ────────────────────────────
        $propiedadesAlquilerDisponibles = Propiedad::with('propietario')
            ->where('estado', 'disponible')
            ->orderBy('ciudad')
            ->orderBy('direccion')
            ->get();

        // ── 7. Propiedades disponibles venta ───────────────────────────────
        $propiedadesVentaDisponibles = PropiedadVenta::with('propietario')
            ->whereIn('estado', ['disponible', 'reservada'])
            ->orderByRaw("FIELD(estado,'disponible','reservada')")
            ->orderBy('ciudad')
            ->get();

        // ── 7b. Propiedades vendidas ────────────────────────────────────────
        $propiedadesVendidas = PropiedadVenta::with('propietario')
            ->where('estado', 'vendida')
            ->orderByDesc('updated_at')
            ->get();

        // ── 8. Contratos venciendo ─────────────────────────────────────────
        $contratosVenciendo = Contrato::with(['propiedad', 'inquilino'])
            ->where('estado', 'activo')
            ->where('fecha_fin', '<=', now()->addDays(90))
            ->orderBy('fecha_fin')
            ->get();

        // ── 9. Pagos vencidos ──────────────────────────────────────────────
        $pagosVencidos = Pago::with(['contrato.inquilino', 'contrato.propiedad'])
            ->where('estado', 'vencido')
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('livewire.reportes', [
            'cobranzaMensual'             => $cobranzaMensual,
            'totalAnio'                   => $totalAnio,
            'pendienteAnio'               => $pendienteAnio,
            'cuotasPendientes'            => $cuotasPendientes,
            'liquidacionesDetalle'        => $liquidacionesDetalle,
            'liquidacionesResumen'        => $liquidacionesResumen,
            'liquidacionesPorPropietario' => $liquidacionesPorPropietario,
            'gastosPorCategoria'          => $gastosPorCategoria,
            'propiedadesAlquilerDisponibles' => $propiedadesAlquilerDisponibles,
            'propiedadesVentaDisponibles' => $propiedadesVentaDisponibles,
            'propiedadesVendidas'         => $propiedadesVendidas,
            'contratosVenciendo'          => $contratosVenciendo,
            'pagosVencidos'               => $pagosVencidos,
            'propietarios'                => Propietario::orderBy('apellido')->get(),
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Contrato;
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
        // ── 1. KPIs ────────────────────────────────────────────────────────
        $totalAnio     = Pago::where('periodo_anio', $this->anio)->where('estado', 'pagado')->sum('total');
        $pendienteAnio = Pago::where('periodo_anio', $this->anio)->whereIn('estado', ['pendiente', 'vencido'])->sum('total');


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

        // ── 4. Propiedades disponibles alquiler ────────────────────────────
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
            'totalAnio'                      => $totalAnio,
            'pendienteAnio'                  => $pendienteAnio,
            'liquidacionesDetalle'           => $liquidacionesDetalle,
            'liquidacionesResumen'           => $liquidacionesResumen,
            'propiedadesAlquilerDisponibles' => $propiedadesAlquilerDisponibles,
            'propiedadesVentaDisponibles'    => $propiedadesVentaDisponibles,
            'propiedadesVendidas'            => $propiedadesVendidas,
            'contratosVenciendo'             => $contratosVenciendo,
            'pagosVencidos'                  => $pagosVencidos,
            'propietarios'                   => Propietario::orderBy('apellido')->get(),
        ]);
    }
}

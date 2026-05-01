<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Contrato;
use App\Models\Inquilino;
use App\Models\Liquidacion;
use App\Models\Pago;
use App\Models\Configuracion;
use App\Models\Propiedad;
use App\Models\PropiedadVenta;
use App\Models\Propietario;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $mesActual = now()->month;
        $anioActual = now()->year;

        $propiedadesAlquiladas  = Propiedad::where('estado', 'alquilada')->count();
        $propiedadesDisponibles = Propiedad::where('estado', 'disponible')->count();
        $totalPropiedades       = Propiedad::count();

        $ventaDisponibles = PropiedadVenta::where('estado', 'disponible')->count();
        $ventaReservadas  = PropiedadVenta::where('estado', 'reservada')->count();
        $ventaValor       = PropiedadVenta::where('estado', 'disponible')->where('moneda', 'USD')->sum('precio_venta');

        $contratosActivos = Contrato::where('estado', 'activo')->count();
        $contratosVencer = Contrato::where('estado', 'activo')
            ->where('fecha_fin', '<=', now()->addDays(60))
            ->count();

        $cobranzaMes = Pago::where('estado', 'pagado')
            ->where('periodo_mes', $mesActual)
            ->where('periodo_anio', $anioActual)
            ->sum('total');

        $pagosPendientes = Pago::where('estado', 'pendiente')->count();
        $pagosVencidos = Pago::where('estado', 'vencido')->count();
        $montoPendiente = Pago::whereIn('estado', ['pendiente', 'vencido'])->sum('total');

        $alquileresVencidos = Pago::with(['contrato.propiedad', 'contrato.inquilino'])
            ->where('estado', 'vencido')
            ->orderBy('fecha_vencimiento')
            ->limit(8)
            ->get();

        $contratosProximoVencer = Contrato::with(['propiedad', 'inquilino'])
            ->where('estado', 'activo')
            ->where('fecha_fin', '<=', now()->addDays(90))
            ->orderBy('fecha_fin')
            ->limit(5)
            ->get();

        $config = Configuracion::get();

        return view('livewire.dashboard', compact(
            'config',
            'alquileresVencidos',
            'propiedadesAlquiladas',
            'propiedadesDisponibles',
            'totalPropiedades',
            'ventaDisponibles',
            'ventaReservadas',
            'ventaValor',
            'contratosActivos',
            'contratosVencer',
            'cobranzaMes',
            'pagosPendientes',
            'pagosVencidos',
            'montoPendiente',
            'contratosProximoVencer',
        ));
    }
}

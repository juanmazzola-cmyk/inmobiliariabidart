<?php

namespace App\Livewire;

use App\Models\Contrato;
use App\Models\Inquilino;
use App\Models\Pago;
use App\Models\Propiedad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Contratos')]
class Contratos extends Component
{
    use WithPagination;

    #[Url]
    public string $busqueda = '';
    #[Url]
    public string $filtroEstado = '';
    #[Url]
    public ?int $abrirConPropiedad = null;

    public bool $modalAbrir = false;
    public ?int $contratoId = null;

    // Campos del formulario
    public ?int $propiedadId = null;
    public ?int $inquilinoId = null;
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public string $montoAlquiler = '';
    public int $diaVencimiento = 10;
    public string $comisionPorcentaje = '0';
    public string $comisionMonto      = '';
    public string $depositoGarantia   = '';
    public string $moneda = 'ARS';
    public bool $incrementoAutomatico = false;
    public string $porcentajeIncremento = '';
    public int $mesesIncremento = 6;
    public string $clausulasAdicionales = '';

    // Solo al editar
    public bool $actualizarCuotas = false;

    // Modal aumento
    public bool $modalAumento = false;
    public ?int $aumentoContratoId = null;
    public string $tipoAumento = 'porcentaje';
    public string $valorAumento = '';
    public string $montoActualAumento = '';
    public string $monedaAumento = 'ARS';
    public bool $actualizarCuotasAumento = true;

    protected function rules(): array
    {
        return [
            'propiedadId'         => 'required|integer',
            'inquilinoId'         => 'required|integer',
            'fechaInicio'         => 'required|date',
            'fechaFin'            => 'required|date|after:fechaInicio',
            'montoAlquiler'       => 'required|numeric|min:1',
            'diaVencimiento'      => 'required|integer|between:1,28',
            'comisionPorcentaje'  => 'required|numeric|between:0,100',
            'depositoGarantia'    => 'nullable|numeric|min:0',
            'porcentajeIncremento'=> 'nullable|numeric|between:0,100',
            'mesesIncremento'     => 'nullable|integer|min:1',
        ];
    }

    protected $messages = [
        'propiedadId.required'    => 'Seleccioná una propiedad.',
        'inquilinoId.required'    => 'Seleccioná un inquilino.',
        'fechaInicio.required'    => 'La fecha de inicio es obligatoria.',
        'fechaFin.required'       => 'La fecha de fin es obligatoria.',
        'fechaFin.after'          => 'La fecha de fin debe ser posterior al inicio.',
        'montoAlquiler.required'  => 'El monto del alquiler es obligatorio.',
        'montoAlquiler.min'       => 'El monto debe ser mayor a cero.',
        'diaVencimiento.between'  => 'El día debe estar entre 1 y 28.',
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        if ($this->abrirConPropiedad) {
            $this->nuevo();
            $this->propiedadId = $this->abrirConPropiedad;
        }
    }

    public function updatedMontoAlquiler(): void
    {
        $raw = preg_replace('/[^\d]/', '', $this->montoAlquiler);
        $this->montoAlquiler = $raw ? number_format((int) $raw, 0, ',', '.') : '';
    }

    public function updatedComisionMonto(): void
    {
        $raw = preg_replace('/[^\d]/', '', $this->comisionMonto);
        $this->comisionMonto = $raw ? number_format((int) $raw, 0, ',', '.') : '';
    }

    public function updatedDepositoGarantia(): void
    {
        $raw = preg_replace('/[^\d]/', '', $this->depositoGarantia);
        $this->depositoGarantia = $raw ? number_format((int) $raw, 0, ',', '.') : '';
    }

    public function updatedValorAumento(): void
    {
        if ($this->tipoAumento === 'valor') {
            $raw = preg_replace('/[^\d]/', '', $this->valorAumento);
            $this->valorAumento = $raw ? number_format((int) $raw, 0, ',', '.') : '';
        }
    }

    public function render()
    {
        $contratos = Contrato::with(['propiedad.propietario', 'inquilino'])
            ->withCount([
                'pagos as cuotas_pendientes' => fn($q) => $q->where('estado', 'pendiente'),
                'pagos as cuotas_vencidas'   => fn($q) => $q->where('estado', 'vencido'),
                'pagos as cuotas_pagadas'    => fn($q) => $q->where('estado', 'pagado'),
            ])
            ->when($this->busqueda, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->whereHas('inquilino', fn($i) =>
                        $i->where('apellido', 'like', "%{$this->busqueda}%")
                          ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                    )->orWhereHas('propiedad', fn($p) =>
                        $p->where('direccion', 'like', "%{$this->busqueda}%")
                          ->orWhere('ciudad', 'like', "%{$this->busqueda}%")
                    )
                )
            )
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->orderByRaw("FIELD(estado, 'activo', 'vencido', 'rescindido', 'renovado')")
            ->orderByDesc('fecha_inicio')
            ->paginate(15);

        $stats = [
            'activos'         => Contrato::where('estado', 'activo')->count(),
            'por_vencer'      => Contrato::where('estado', 'activo')
                                    ->where('fecha_fin', '<=', now()->addDays(60))
                                    ->count(),
            'vencidos'        => Contrato::where('estado', 'vencido')->count(),
            'rescindidos'     => Contrato::where('estado', 'rescindido')->count(),
        ];

        // Propietario info para mostrar en el modal al seleccionar propiedad
        $propiedadSeleccionada = $this->propiedadId
            ? Propiedad::with('propietario')->find($this->propiedadId)
            : null;

        // Preview monto nuevo en modal aumento
        $montoNuevo = null;
        if ($this->modalAumento && $this->montoActualAumento !== '' && $this->valorAumento !== '') {
            $actual      = (float) $this->montoActualAumento;
            $valorLimpio = $this->tipoAumento === 'porcentaje'
                ? (float) $this->valorAumento
                : (float) str_replace('.', '', $this->valorAumento);
            if ($valorLimpio > 0) {
                $montoNuevo = $this->tipoAumento === 'porcentaje'
                    ? round($actual * (1 + $valorLimpio / 100), 2)
                    : round($actual + $valorLimpio, 2);
            }
        }

        return view('livewire.contratos', [
            'contratos'            => $contratos,
            'stats'                => $stats,
            'montoNuevo'           => $montoNuevo,
            'propiedadesDisponibles' => Propiedad::with('propietario')
                ->where(fn($q) => $q->where('estado', 'disponible')
                    ->orWhere('id', $this->propiedadId)
                )
                ->orderBy('ciudad')
                ->orderBy('direccion')
                ->get(),
            'inquilinos'           => Inquilino::where('activo', true)->orderBy('apellido')->get(),
            'propiedadSeleccionada'=> $propiedadSeleccionada,
        ]);
    }

    public function nuevo(): void
    {
        $this->resetForm();
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin    = now()->startOfMonth()->addYear()->subDay()->format('Y-m-d');
        $this->modalAbrir  = true;
    }

    public function editar(int $id): void
    {
        $c = Contrato::findOrFail($id);
        $this->contratoId           = $id;
        $this->propiedadId          = $c->propiedad_id;
        $this->inquilinoId          = $c->inquilino_id;
        $this->fechaInicio          = $c->fecha_inicio->format('Y-m-d');
        $this->fechaFin             = $c->fecha_fin->format('Y-m-d');
        $this->montoAlquiler        = number_format((float) $c->monto_alquiler, 0, ',', '.');
        $this->diaVencimiento       = $c->dia_vencimiento;
        $this->comisionPorcentaje   = $c->comision_porcentaje;
        $this->comisionMonto        = $c->comision_monto ? number_format((float) $c->comision_monto, 0, ',', '.') : '';
        $this->depositoGarantia     = $c->deposito_garantia ? number_format((float) $c->deposito_garantia, 0, ',', '.') : '';
        $this->moneda               = $c->moneda;
        $this->incrementoAutomatico = $c->incremento_automatico;
        $this->porcentajeIncremento = $c->porcentaje_incremento ?? '';
        $this->mesesIncremento      = $c->meses_incremento ?? 6;
        $this->clausulasAdicionales = $c->clausulas_adicionales ?? '';
        $this->actualizarCuotas     = false;
        $this->modalAbrir           = true;
    }

    public function guardar(): void
    {
        // Limpiar separadores de miles antes de validar y operar
        $this->montoAlquiler    = (string)(int) str_replace('.', '', $this->montoAlquiler);
        $this->comisionMonto    = $this->comisionMonto ? (string)(int) str_replace('.', '', $this->comisionMonto) : '';
        $this->depositoGarantia = $this->depositoGarantia ? (string)(int) str_replace('.', '', $this->depositoGarantia) : '';

        $this->validate();

        $monto    = (float) $this->montoAlquiler;
        $deposito = $this->depositoGarantia !== '' ? (float) $this->depositoGarantia : 0;

        $datos = [
            'propiedad_id'         => $this->propiedadId,
            'inquilino_id'         => $this->inquilinoId,
            'fecha_inicio'         => $this->fechaInicio,
            'fecha_fin'            => $this->fechaFin,
            'monto_alquiler'       => $monto,
            'dia_vencimiento'      => $this->diaVencimiento,
            'comision_porcentaje'  => (float) $this->comisionPorcentaje,
            'comision_monto'       => $this->comisionMonto !== '' ? (float) $this->comisionMonto : null,
            'deposito_garantia'    => $deposito,
            'moneda'               => $this->moneda,
            'estado'               => 'activo',
            'incremento_automatico'=> $this->incrementoAutomatico,
            'porcentaje_incremento'=> $this->incrementoAutomatico ? (float) $this->porcentajeIncremento : null,
            'meses_incremento'     => $this->incrementoAutomatico ? $this->mesesIncremento : null,
            'clausulas_adicionales'=> $this->clausulasAdicionales ?: null,
        ];

        if ($this->contratoId) {
            $contrato = Contrato::findOrFail($this->contratoId);
            $montoAnterior = (float) $contrato->monto_alquiler;
            $contrato->update($datos);

            if ($this->actualizarCuotas && $monto !== $montoAnterior) {
                Pago::where('contrato_id', $this->contratoId)
                    ->where('estado', 'pendiente')
                    ->update(['monto' => $monto, 'total' => $monto]);
            }
            $msg = 'Contrato actualizado correctamente.';
        } else {
            $contrato = Contrato::create($datos);

            // Marcar propiedad como alquilada
            Propiedad::findOrFail($this->propiedadId)->update(['estado' => 'alquilada']);

            // Generar cuotas mensuales
            $this->generarCuotasMensuales($contrato);

            $cuotas = $this->calcularCantidadCuotas();
            $msg = "Contrato creado. Se generaron {$cuotas} cuotas mensuales.";
        }

        $this->cerrarModal();
        session()->flash('success', $msg);
    }

    public function rescindir(int $id): void
    {
        $contrato = Contrato::findOrFail($id);
        $contrato->update(['estado' => 'rescindido']);
        $contrato->propiedad()->update(['estado' => 'disponible']);
        session()->flash('success', 'Contrato rescindido. La propiedad quedó disponible.');
    }

    public function eliminar(int $id): void
    {
        $contrato = Contrato::findOrFail($id);
        // Liberar propiedad si estaba alquilada
        if (in_array($contrato->estado, ['activo', 'vencido'])) {
            $contrato->propiedad()->update(['estado' => 'disponible']);
        }
        // El cascade en BD elimina pagos y liquidaciones automáticamente
        $contrato->delete();
        session()->flash('success', 'Contrato eliminado junto con sus pagos y liquidaciones.');
    }

    public function abrirModalAumento(int $id): void
    {
        $c = Contrato::findOrFail($id);
        $this->aumentoContratoId      = $id;
        $this->montoActualAumento     = (string) $c->monto_alquiler;
        $this->monedaAumento          = $c->moneda;
        $this->tipoAumento            = 'porcentaje';
        $this->valorAumento           = '';
        $this->actualizarCuotasAumento = true;
        $this->modalAumento           = true;
    }

    public function aplicarAumento(): void
    {
        $this->validate([
            'valorAumento' => 'required|numeric|min:0.01',
        ], [
            'valorAumento.required' => 'Ingresá el valor del aumento.',
            'valorAumento.min'      => 'El valor debe ser mayor a cero.',
        ]);

        $contrato    = Contrato::findOrFail($this->aumentoContratoId);
        $actual      = (float) $contrato->monto_alquiler;
        $valorLimpio = $this->tipoAumento === 'porcentaje'
            ? (float) $this->valorAumento
            : (float) str_replace('.', '', $this->valorAumento);
        $nuevoMonto  = $this->tipoAumento === 'porcentaje'
            ? round($actual * (1 + $valorLimpio / 100), 2)
            : round($actual + $valorLimpio, 2);

        $contrato->update(['monto_alquiler' => $nuevoMonto]);

        if ($this->actualizarCuotasAumento) {
            $pagos = Pago::where('contrato_id', $this->aumentoContratoId)
                ->whereIn('estado', ['pendiente', 'vencido'])
                ->get();

            foreach ($pagos as $pago) {
                $nuevoMontoCuota = $this->tipoAumento === 'porcentaje'
                    ? round($pago->monto * (1 + $valorLimpio / 100))
                    : round($pago->monto + $valorLimpio);

                $pago->update([
                    'monto' => $nuevoMontoCuota,
                    'total' => $nuevoMontoCuota + $pago->recargo - $pago->descuento,
                ]);
            }
        }

        $this->cerrarModalAumento();
        session()->flash('success', 'Alquiler actualizado. Base nueva: $ ' . number_format($nuevoMonto, 0, ',', '.') . '.');
    }

    public function cerrarModalAumento(): void
    {
        $this->modalAumento       = false;
        $this->aumentoContratoId  = null;
        $this->montoActualAumento = '';
        $this->valorAumento       = '';
        $this->resetValidation();
    }

    public function cerrarModal(): void
    {
        $this->modalAbrir = false;
        $this->resetValidation();
        $this->resetForm();
    }

    public function ejecutarIncrementos(): void
    {
        Artisan::call('contratos:incrementar');
        $output = trim(Artisan::output());
        session()->flash('success', $output ?: 'Incrementos procesados.');
    }

    private function generarCuotasMensuales(Contrato $contrato): void
    {
        $cursor  = Carbon::parse($contrato->fecha_inicio)->startOfMonth();
        $fin     = Carbon::parse($contrato->fecha_fin);
        $dia     = $contrato->dia_vencimiento;
        $monto   = (float) $contrato->monto_alquiler;
        $mes     = 0;

        $conIncremento = $contrato->incremento_automatico
            && $contrato->porcentaje_incremento > 0
            && $contrato->meses_incremento > 0;

        while ($cursor->lte($fin)) {
            if ($conIncremento && $mes > 0 && $mes % $contrato->meses_incremento === 0) {
                $monto = round($monto * (1 + $contrato->porcentaje_incremento / 100));
            }

            $diaReal     = min($dia, $cursor->daysInMonth);
            $vencimiento = Carbon::create($cursor->year, $cursor->month, $diaReal);

            Pago::create([
                'contrato_id'       => $contrato->id,
                'fecha_vencimiento' => $vencimiento,
                'fecha_pago'        => null,
                'periodo_mes'       => $cursor->month,
                'periodo_anio'      => $cursor->year,
                'monto'             => $monto,
                'recargo'           => 0,
                'descuento'         => 0,
                'total'             => $monto,
                'medio_pago'        => 'efectivo',
                'estado'            => 'pendiente',
            ]);

            $cursor->addMonth();
            $mes++;
        }
    }

    private function calcularCantidadCuotas(): int
    {
        if (!$this->fechaInicio || !$this->fechaFin) return 0;
        $inicio = Carbon::parse($this->fechaInicio)->startOfMonth();
        $fin    = Carbon::parse($this->fechaFin);
        return $fin->gt($inicio) ? $inicio->diffInMonths($fin) + 1 : 0;
    }

    private function resetForm(): void
    {
        $this->contratoId           = null;
        $this->propiedadId          = null;
        $this->inquilinoId          = null;
        $this->fechaInicio          = '';
        $this->fechaFin             = '';
        $this->montoAlquiler        = '';
        $this->diaVencimiento       = 10;
        $this->comisionPorcentaje   = '0';
        $this->comisionMonto        = '';
        $this->depositoGarantia     = '';
        $this->moneda               = 'ARS';
        $this->incrementoAutomatico = false;
        $this->porcentajeIncremento = '';
        $this->mesesIncremento      = 6;
        $this->clausulasAdicionales = '';
        $this->actualizarCuotas     = false;
    }
}

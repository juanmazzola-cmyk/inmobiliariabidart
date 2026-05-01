<div class="space-y-6">

    {{-- Filtros globales --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-3 items-center">
        <span class="text-sm font-medium text-gray-600">Filtrar por:</span>
        <select wire:model.live="anio" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach(range(now()->year, 2023) as $a)
                <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
        <select wire:model.live="mes" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">Todos los meses</option>
            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $nombre)
                <option value="{{ $i + 1 }}">{{ $nombre }}</option>
            @endforeach
        </select>
        <select wire:model.live="propietarioId" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los propietarios</option>
            @foreach($propietarios as $p)
                <option value="{{ $p->id }}">{{ $p->nombre_completo }}</option>
            @endforeach
        </select>
    </div>

    {{-- KPI anual --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobranza Año {{ $anio }}</p>
            <p class="text-2xl font-bold text-green-600 mt-1">$ {{ number_format($totalAnio, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pendiente / Vencido</p>
            <p class="text-2xl font-bold text-red-600 mt-1">$ {{ number_format($pendienteAnio, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Efectividad de cobro</p>
            @php $efectividad = ($totalAnio + $pendienteAnio) > 0 ? round($totalAnio / ($totalAnio + $pendienteAnio) * 100, 1) : 0; @endphp
            <p class="text-2xl font-bold {{ $efectividad >= 80 ? 'text-green-600' : 'text-orange-600' }} mt-1">{{ $efectividad }}%</p>
        </div>
    </div>

    {{-- ── Sección 1: Cobranza mensual ─────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Cuotas cobradas por período — {{ $anio }}</h3>
            <a href="{{ route('pagos.index') }}" class="text-xs text-blue-600 hover:underline">Ver listado completo →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Mes</th>
                        <th class="px-4 py-3 text-right">Cobrado</th>
                        <th class="px-4 py-3 text-right">Pendiente / Vencido</th>
                        <th class="px-4 py-3 text-left w-48">Proporción</th>
                        <th class="px-4 py-3 text-right">% Cobrado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cobranzaMensual as $row)
                    @php
                        $total = $row['cobrado'] + $row['pendiente'];
                        $pct   = $total > 0 ? round($row['cobrado'] / $total * 100) : 0;
                        $esHoy = $row['mes'] === now()->month && $anio === now()->year;
                    @endphp
                    <tr class="{{ $esHoy ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $row['nombre'] }}
                            @if ($esHoy) <span class="ml-1 text-xs text-blue-500 font-normal">(actual)</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $row['cobrado'] > 0 ? 'text-green-600' : 'text-gray-300' }}">
                            {{ $row['cobrado'] > 0 ? '$ ' . number_format($row['cobrado'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right {{ $row['pendiente'] > 0 ? 'text-red-500' : 'text-gray-300' }}">
                            {{ $row['pendiente'] > 0 ? '$ ' . number_format($row['pendiente'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($total > 0)
                            <div class="flex h-3 rounded overflow-hidden bg-gray-100">
                                @if($row['cobrado'] > 0)
                                    <div class="bg-green-500 h-full" style="width:{{ $pct }}%"></div>
                                @endif
                                @if($row['pendiente'] > 0)
                                    <div class="bg-red-300 h-full" style="width:{{ 100 - $pct }}%"></div>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $pct > 0 ? $pct.'%' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Sección 2: Cuotas pendientes/vencidas ──────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Cuotas pendientes y vencidas
                @if($cuotasPendientes->count() > 0)
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                        {{ $cuotasPendientes->count() }}
                    </span>
                @endif
            </h3>
            <span class="text-xs text-gray-400">Total: $ {{ number_format($cuotasPendientes->sum('total'), 0, ',', '.') }}</span>
        </div>
        @if($cuotasPendientes->isEmpty())
            <div class="px-5 py-10 text-center text-gray-400 text-sm">No hay cuotas pendientes para el período seleccionado.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Inquilino</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-center">Período</th>
                        <th class="px-4 py-3 text-center">Vencimiento</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cuotasPendientes as $pago)
                    <tr class="{{ $pago->estado === 'vencido' ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pago->contrato->inquilino->nombre_completo }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $pago->contrato->propiedad->tipo_label }}<br>{{ $pago->contrato->propiedad->ciudad }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $pago->periodo_label }}</td>
                        <td class="px-4 py-3 text-center text-xs font-medium {{ $pago->estado === 'vencido' ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                            @if($pago->estado === 'vencido')
                                <br><span class="text-red-500">+{{ (int) now()->diffInDays($pago->fecha_vencimiento) }}d</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $pago->estado === 'vencido' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $pago->estado === 'vencido' ? 'Vencida' : 'Pendiente' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold {{ $pago->estado === 'vencido' ? 'text-red-700' : 'text-gray-900' }}">
                            $ {{ number_format($pago->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('pagos.index') }}?filtroContrato={{ $pago->contrato_id }}"
                                class="text-xs text-blue-600 hover:underline">Cobrar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Sección 3: Pagos a propietarios por período ─────────���───────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">Liquidaciones a propietarios — {{ $mes > 0 ? ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][$mes] . ' ' : '' }}{{ $anio }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Pagado: <span class="text-green-600 font-medium">$ {{ number_format($liquidacionesResumen['pagadas'], 0, ',', '.') }}</span>
                    · Pendiente: <span class="text-yellow-600 font-medium">$ {{ number_format($liquidacionesResumen['pendientes'], 0, ',', '.') }}</span>
                    ({{ $liquidacionesResumen['cantidad_pend'] }} liq.)
                </p>
            </div>
            <a href="{{ route('liquidaciones.index') }}" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
        </div>
        @if($liquidacionesDetalle->isEmpty())
            <div class="px-5 py-10 text-center text-gray-400 text-sm">Sin liquidaciones para el período seleccionado.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Propietario</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-center">Período</th>
                        <th class="px-4 py-3 text-right">Alquiler</th>
                        <th class="px-4 py-3 text-right">Comisión</th>
                        <th class="px-4 py-3 text-right font-bold">Neto</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($liquidacionesDetalle as $liq)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $liq->propietario->nombre_completo }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $liq->propiedad->tipo_label }}<br>{{ $liq->propiedad->ciudad }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $liq->periodo_label }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">$ {{ number_format($liq->monto_alquiler, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-500">- $ {{ number_format($liq->monto_comision, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">$ {{ number_format($liq->monto_neto, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ match($liq->estado) {
                                    'pagada'   => 'bg-green-100 text-green-700',
                                    'emitida'  => 'bg-blue-100 text-blue-700',
                                    default    => 'bg-gray-100 text-gray-500',
                                } }}">
                                {{ ucfirst($liq->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('liquidaciones.pdf', $liq->id) }}" target="_blank"
                                class="text-xs text-blue-600 hover:text-blue-800" title="Descargar PDF">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Sección 4: Propiedades disponibles ──────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Alquiler disponible --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800">Propiedades en Alquiler Disponibles</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $propiedadesAlquilerDisponibles->count() }} propiedad/es</p>
                </div>
                <a href="{{ route('propiedades.index') }}?filtroEstado=disponible" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
            </div>
            @if($propiedadesAlquilerDisponibles->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-gray-400">No hay propiedades disponibles.</div>
            @else
                <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                    @foreach($propiedadesAlquilerDisponibles as $p)
                    <div class="px-5 py-3 flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $p->tipo_label }}</p>
                            <p class="text-xs text-gray-500">{{ $p->direccion_completa }}</p>
                            @if($p->propietario)
                                <p class="text-xs text-gray-400">Prop.: {{ $p->propietario->nombre_completo }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if($p->ambientes)
                                <p class="text-xs text-gray-500">{{ $p->ambientes }} amb.</p>
                            @endif
                            @if($p->superficie_total)
                                <p class="text-xs text-gray-400">{{ number_format($p->superficie_total, 0) }} m²</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Venta disponible --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800">Propiedades en Venta Disponibles / Reservadas</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $propiedadesVentaDisponibles->count() }} propiedad/es</p>
                </div>
                <a href="{{ route('propiedades-venta.index') }}?filtroEstado=disponible" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
            </div>
            @if($propiedadesVentaDisponibles->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-gray-400">No hay propiedades en venta.</div>
            @else
                <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                    @foreach($propiedadesVentaDisponibles as $p)
                    <div class="px-5 py-3 flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $p->tipo_label }}</p>
                                @if($p->estado === 'reservada')
                                    <span class="px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700">Reservada</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $p->direccion_completa }}</p>
                            @if($p->propietario)
                                <p class="text-xs text-gray-400">Prop.: {{ $p->propietario->nombre_completo }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-blue-600">{{ $p->precio_formateado }}</p>
                            @if($p->superficie_total)
                                <p class="text-xs text-gray-400">{{ number_format($p->superficie_total, 0) }} m²</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Sección 4b: Propiedades vendidas ──────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">Propiedades Vendidas</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $propiedadesVendidas->count() }} propiedad/es</p>
            </div>
            <a href="{{ route('propiedades-venta.index') }}?filtroEstado=vendida" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
        </div>
        @if($propiedadesVendidas->isEmpty())
            <div class="px-5 py-8 text-center text-sm text-gray-400">No hay propiedades vendidas registradas.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Dirección</th>
                        <th class="px-4 py-3 text-left">Propietario</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-center">Sup.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($propiedadesVendidas as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->tipo_label }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $p->direccion_completa }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $p->propietario?->nombre_completo ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">{{ $p->precio_formateado }}</td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">
                            {{ $p->superficie_total ? number_format($p->superficie_total, 0) . ' m²' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Sección 5: Rendición por propietario (resumen) ──────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Rendición por Propietario — {{ $anio }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Propietario</th>
                            <th class="px-4 py-3 text-right">Alquiler</th>
                            <th class="px-4 py-3 text-right">Comisión</th>
                            <th class="px-4 py-3 text-right">Neto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($liquidacionesPorPropietario as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row['propietario'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">$ {{ number_format($row['total_alquiler'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-red-500">- $ {{ number_format($row['total_comision'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">$ {{ number_format($row['total_neto'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Sin datos para el período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Gastos Deducibles por Categoría — {{ $anio }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Categoría</th>
                            <th class="px-4 py-3 text-center">Cantidad</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($gastosPorCategoria as $g)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ match($g->categoria) {
                                    'reparacion'    => 'Reparación',
                                    'expensas'      => 'Expensas',
                                    'impuesto'      => 'Impuesto',
                                    'servicio'      => 'Servicio',
                                    'administracion'=> 'Administración',
                                    default         => 'Otro'
                                } }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $g->cantidad }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">$ {{ number_format($g->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">Sin gastos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Sección 6: Alertas ───────────────────────────────────────────────── --}}
    @if($pagosVencidos->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-red-100 bg-red-50 flex items-center justify-between">
            <h3 class="font-semibold text-red-800">Cuotas Vencidas sin Cobrar ({{ $pagosVencidos->count() }})</h3>
            <span class="text-sm font-bold text-red-700">$ {{ number_format($pagosVencidos->sum('total'), 0, ',', '.') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Inquilino</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-center">Período</th>
                        <th class="px-4 py-3 text-center">Venció</th>
                        <th class="px-4 py-3 text-center">Días</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pagosVencidos as $pago)
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pago->contrato->inquilino->nombre_completo }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $pago->contrato->propiedad->tipo_label }}, {{ $pago->contrato->propiedad->ciudad }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $pago->periodo_label }}</td>
                        <td class="px-4 py-3 text-center text-red-600 font-medium">{{ $pago->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                +{{ (int) now()->diffInDays($pago->fecha_vencimiento) }}d
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-red-700">$ {{ number_format($pago->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($contratosVenciendo->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-orange-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-orange-100 bg-orange-50">
            <h3 class="font-semibold text-orange-800">Contratos que vencen en los próximos 90 días ({{ $contratosVenciendo->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Inquilino</th>
                        <th class="px-4 py-3 text-left">Propiedad</th>
                        <th class="px-4 py-3 text-center">Vencimiento</th>
                        <th class="px-4 py-3 text-center">Días restantes</th>
                        <th class="px-4 py-3 text-right">Alquiler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($contratosVenciendo as $c)
                    @php $dias = (int) now()->diffInDays($c->fecha_fin); @endphp
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $c->inquilino->nombre_completo }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $c->propiedad->tipo_label }}, {{ $c->propiedad->ciudad }}</td>
                        <td class="px-4 py-3 text-center font-medium {{ $dias < 30 ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $c->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $dias < 30 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $dias }} días
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">$ {{ number_format($c->monto_alquiler, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

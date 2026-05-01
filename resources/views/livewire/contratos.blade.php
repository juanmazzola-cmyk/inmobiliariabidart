<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Activos</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['activos'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Por vencer <span class="normal-case">(60 días)</span></p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ $stats['por_vencer'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Vencidos</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $stats['vencidos'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Rescindidos</p>
            <p class="text-2xl font-bold text-gray-400 mt-1">{{ $stats['rescindidos'] }}</p>
        </div>
    </div>

    {{-- Filtros + botón --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6 items-start sm:items-center">
        <input wire:model.live="busqueda" type="text" placeholder="Buscar por inquilino o dirección..."
            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 max-w-sm">
        <select wire:model.live="filtroEstado"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los estados</option>
            <option value="activo">Activo</option>
            <option value="vencido">Vencido</option>
            <option value="rescindido">Rescindido</option>
            <option value="renovado">Renovado</option>
        </select>
        <button wire:click="nuevo"
            class="ml-auto flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Contrato
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Inmueble / Propietario</th>
                        <th class="px-4 py-3 text-left">Inquilino</th>
                        <th class="px-4 py-3 text-center">Período</th>
                        <th class="px-4 py-3 text-right">Alquiler mensual</th>
                        <th class="px-4 py-3 text-center">Cuotas</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($contratos as $c)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 text-xs">{{ $c->propiedad->tipo_label }}</p>
                                <p class="text-xs text-gray-600">{{ $c->propiedad->direccion_completa }}</p>
                                <p class="text-xs text-gray-400">{{ $c->propiedad->propietario->nombre_completo }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $c->inquilino->nombre_completo }}</p>
                                @if ($c->inquilino->telefono)
                                    <p class="text-xs text-gray-400">{{ $c->inquilino->telefono }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs">
                                <p class="text-gray-700">{{ $c->fecha_inicio->format('d/m/Y') }}</p>
                                <p class="text-gray-400">al</p>
                                <p class="{{ $c->estado === 'activo' && $c->fecha_fin->diffInDays(now()) < 60 ? 'text-orange-600 font-semibold' : 'text-gray-700' }}">
                                    {{ $c->fecha_fin->format('d/m/Y') }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-semibold text-gray-900">
                                    {{ $c->moneda === 'USD' ? 'U$S' : '$' }}
                                    {{ number_format($c->monto_alquiler, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">Vto. día {{ $c->dia_vencimiento }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col items-center gap-0.5 text-xs">
                                    @if ($c->cuotas_pendientes > 0)
                                        <span class="text-yellow-600">{{ $c->cuotas_pendientes }} pend.</span>
                                    @endif
                                    @if ($c->cuotas_vencidas > 0)
                                        <span class="text-red-600 font-semibold">{{ $c->cuotas_vencidas }} venc.</span>
                                    @endif
                                    @if ($c->cuotas_pagadas > 0)
                                        <span class="text-green-600">{{ $c->cuotas_pagadas }} pag.</span>
                                    @endif
                                    @if ($c->cuotas_pendientes + $c->cuotas_vencidas + $c->cuotas_pagadas === 0)
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ match($c->estado) {
                                        'activo'     => 'bg-green-100 text-green-700',
                                        'vencido'    => 'bg-red-100 text-red-700',
                                        'rescindido' => 'bg-gray-100 text-gray-500',
                                        default      => 'bg-blue-100 text-blue-700',
                                    } }}">
                                    {{ ucfirst($c->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Ver pagos --}}
                                    <a href="{{ route('pagos.index') }}?filtroContrato={{ $c->id }}"
                                        class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Ver cuotas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </a>
                                    {{-- Editar --}}
                                    <button wire:click="editar({{ $c->id }})"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    {{-- Aumentar alquiler (solo activos/vencidos) --}}
                                    @if (in_array($c->estado, ['activo', 'vencido']))
                                        <button wire:click="abrirModalAumento({{ $c->id }})"
                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Aumentar alquiler">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                            </svg>
                                        </button>
                                    @endif
                                    {{-- Rescindir (solo activos/vencidos) --}}
                                    @if (in_array($c->estado, ['activo', 'vencido']))
                                        <button wire:click="rescindir({{ $c->id }})"
                                            wire:confirm="¿Rescindir este contrato? La propiedad volverá a estar disponible."
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Rescindir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                No se encontraron contratos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contratos->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $contratos->links() }}</div>
        @endif
    </div>

    {{-- Modal Aumento --}}
    @if ($modalAumento)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Aumentar alquiler</h3>
                    <button wire:click="cerrarModalAumento" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="px-4 py-3 bg-gray-50 rounded-xl text-sm flex items-center justify-between">
                        <span class="text-gray-500">Monto actual:</span>
                        <span class="font-bold text-gray-900">
                            {{ $monedaAumento === 'USD' ? 'U$S' : '$' }}
                            {{ number_format((float)$montoActualAumento, 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Tipo de aumento</label>
                        <div class="flex gap-5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="tipoAumento" type="radio" value="porcentaje" class="text-blue-600">
                                <span class="text-sm text-gray-700">Porcentaje (%)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="tipoAumento" type="radio" value="valor" class="text-blue-600">
                                <span class="text-sm text-gray-700">Valor fijo ({{ $monedaAumento === 'USD' ? 'U$S' : '$' }})</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            {{ $tipoAumento === 'porcentaje' ? 'Porcentaje de aumento' : 'Monto a sumar' }}
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                {{ $tipoAumento === 'porcentaje' ? '%' : ($monedaAumento === 'USD' ? 'U$S' : '$') }}
                            </span>
                            <input wire:model.blur="valorAumento" type="text" inputmode="numeric"
                                placeholder="{{ $tipoAumento === 'porcentaje' ? 'ej. 15' : 'ej. 5.000' }}"
                                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('valorAumento') border-red-400 @enderror">
                        </div>
                        @error('valorAumento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if ($montoNuevo !== null)
                        <div class="px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-xl text-sm flex items-center justify-between">
                            <span class="text-emerald-700">Nuevo monto:</span>
                            <span class="font-bold text-emerald-800 text-base">
                                {{ $monedaAumento === 'USD' ? 'U$S' : '$' }}
                                {{ number_format($montoNuevo, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 px-4 py-3 bg-yellow-50 border border-yellow-100 rounded-xl">
                        <input wire:model="actualizarCuotasAumento" type="checkbox" id="actualizarCuotasAumento"
                            class="rounded border-gray-300 text-yellow-600">
                        <label for="actualizarCuotasAumento" class="text-sm text-yellow-800 cursor-pointer">
                            Actualizar cuotas <strong>pendientes y vencidas</strong> con el nuevo monto
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" wire:click="cerrarModalAumento"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="button" wire:click="aplicarAumento"
                            class="px-5 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                            Aplicar aumento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal --}}
    @if ($modalAbrir)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $contratoId ? 'Editar Contrato' : 'Nuevo Contrato de Alquiler' }}
                    </h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="guardar" class="px-6 py-5 space-y-5">

                    {{-- Propiedad e inquilino --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Propiedad *</label>
                            <select wire:model.live="propiedadId"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('propiedadId') border-red-400 @enderror">
                                <option value="">— Seleccioná una propiedad —</option>
                                @foreach ($propiedadesDisponibles as $prop)
                                    <option value="{{ $prop->id }}">
                                        {{ $prop->tipo_label }} — {{ $prop->direccion_completa }}
                                        ({{ $prop->propietario->apellido }})
                                    </option>
                                @endforeach
                            </select>
                            @error('propiedadId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            {{-- Propietario del inmueble --}}
                            @if ($propiedadSeleccionada)
                                <div class="mt-2 px-3 py-2 bg-blue-50 rounded-lg text-xs text-blue-700">
                                    <span class="font-semibold">Propietario:</span>
                                    {{ $propiedadSeleccionada->propietario->nombre_completo }}
                                    @if ($propiedadSeleccionada->propietario->telefono)
                                        · {{ $propiedadSeleccionada->propietario->telefono }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Inquilino *</label>
                            <select wire:model="inquilinoId"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('inquilinoId') border-red-400 @enderror">
                                <option value="">— Seleccioná un inquilino —</option>
                                @foreach ($inquilinos as $inq)
                                    <option value="{{ $inq->id }}">{{ $inq->nombre_completo }} — DNI {{ $inq->dni }}</option>
                                @endforeach
                            </select>
                            @error('inquilinoId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha inicio *</label>
                            <input wire:model.live="fechaInicio" type="date"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fechaInicio') border-red-400 @enderror">
                            @error('fechaInicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha fin *</label>
                            <input wire:model.live="fechaFin" type="date"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fechaFin') border-red-400 @enderror">
                            @error('fechaFin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Monto, día vencimiento y moneda --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Monto alquiler *</label>
                            <input wire:model.blur="montoAlquiler" type="text" inputmode="numeric"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('montoAlquiler') border-red-400 @enderror">
                            @error('montoAlquiler') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Moneda</label>
                            <select wire:model="moneda"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="ARS">Pesos (ARS)</option>
                                <option value="USD">Dólares (USD)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Día vencimiento *</label>
                            <input wire:model="diaVencimiento" type="number" min="1" max="28"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-0.5">Entre 1 y 28</p>
                        </div>
                    </div>

                    {{-- Comisión y depósito --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Comisión %</label>
                            <input wire:model="comisionPorcentaje" type="number" step="0.01" min="0" max="100"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Comisión $</label>
                            <input wire:model.blur="comisionMonto" type="text" inputmode="numeric"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Depósito de garantía</label>
                            <input wire:model.blur="depositoGarantia" type="text" inputmode="numeric"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Incremento automático --}}
                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50">
                        <label class="flex items-center gap-2 cursor-pointer mb-3">
                            <input wire:model.live="incrementoAutomatico" type="checkbox"
                                class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm font-medium text-gray-700">Incremento automático de alquiler</span>
                        </label>
                        @if ($incrementoAutomatico)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Porcentaje de incremento %</label>
                                    <input wire:model="porcentajeIncremento" type="number" step="0.01" min="0"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Cada cuántos meses</label>
                                    <input wire:model="mesesIncremento" type="number" min="1"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Cláusulas adicionales --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cláusulas adicionales</label>
                        <textarea wire:model="clausulasAdicionales" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            placeholder="Condiciones especiales del contrato..."></textarea>
                    </div>

                    {{-- Actualizar cuotas (solo al editar) --}}
                    @if ($contratoId)
                        <div class="flex items-center gap-2 px-4 py-3 bg-yellow-50 border border-yellow-100 rounded-xl">
                            <input wire:model="actualizarCuotas" type="checkbox" id="actualizarCuotas"
                                class="rounded border-gray-300 text-yellow-600">
                            <label for="actualizarCuotas" class="text-sm text-yellow-800 cursor-pointer">
                                Actualizar el monto de todas las cuotas <strong>pendientes</strong> con el nuevo valor
                            </label>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" wire:click="cerrarModal"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            {{ $contratoId ? 'Guardar cambios' : 'Crear contrato y generar cuotas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<div>
    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobrado</p>
            <p class="text-2xl font-bold text-green-600 mt-1">$ {{ number_format($totalCobrado, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Pendiente / Vencido</p>
            <div class="flex gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Pendiente</p>
                    <p class="text-xl font-bold text-yellow-600">$ {{ number_format($totalPendiente, 0, ',', '.') }}</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Vencido</p>
                    <p class="text-xl font-bold text-red-600">$ {{ number_format($totalVencido, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Filtro activo</p>
            <p class="text-sm font-medium text-gray-700 mt-2">
                @if($filtroMes && $filtroAnio)
                    @php $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp
                    {{ $meses[$filtroMes] }} {{ $filtroAnio }}
                @elseif($filtroAnio)
                    Año {{ $filtroAnio }}
                @else
                    Todos los períodos
                @endif
            </p>
        </div>
    </div>

    {{-- Filtros + botón --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input wire:model.live="busqueda" type="text" placeholder="Buscar inquilino..."
            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select wire:model.live="filtroMes" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">Todos los meses</option>
            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                <option value="{{ $i + 1 }}">{{ $mes }}</option>
            @endforeach
        </select>

        <select wire:model.live="filtroAnio" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">Todos los años</option>
            @foreach(range(now()->year, 2023) as $anio)
                <option value="{{ $anio }}">{{ $anio }}</option>
            @endforeach
        </select>

        <select wire:model.live="filtroEstado" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos</option>
            <option value="pagado">Pagado</option>
            <option value="pendiente">Pendiente</option>
            <option value="vencido">Vencido</option>
        </select>

        <button wire:click="nuevoPago"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar Pago
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Inquilino</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-center">Período</th>
                        <th class="px-4 py-3 text-center">Vencimiento</th>
                        <th class="px-4 py-3 text-center">Pago</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Medio</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pagos as $pago)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $pago->contrato->inquilino->nombre_completo }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $pago->contrato->propiedad->direccion_completa }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $pago->periodo_label }}</td>
                        <td class="px-4 py-3 text-center text-xs
                            {{ $pago->estado === 'vencido' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            $ {{ number_format($pago->monto, 0, ',', '.') }}
                            @if($pago->recargo > 0)
                                <span class="text-red-500 text-xs">+{{ number_format($pago->recargo, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            $ {{ number_format($pago->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $pago->medio_pago_label }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $pago->estado === 'pagado'   ? 'bg-green-100 text-green-800' :
                                   ($pago->estado === 'vencido' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ match($pago->estado) {
                                    'pagado'   => 'Pagado',
                                    'vencido'  => 'Vencido',
                                    'pendiente'=> 'Pendiente',
                                    default    => 'Parcial'
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if ($pago->estado !== 'pagado')
                                    <button wire:click="abrirModalCobro({{ $pago->id }})"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Cobrar
                                    </button>
                                @else
                                    <button wire:click="editarPago({{ $pago->id }})"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </button>
                                    <a href="{{ route('pagos.recibo', $pago->id) }}" target="_blank"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Recibo
                                    </a>
                                    <a href="{{ route('liquidaciones.index', ['desdePagoId' => $pago->id]) }}"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors"
                                        title="Crear liquidación al propietario">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Liquidar
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-gray-400">No hay pagos que coincidan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pagos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $pagos->links() }}</div>
        @endif
    </div>

    {{-- Modal nuevo pago --}}
    @if($modalAbrir)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h3 class="font-semibold text-gray-800">
                    @if (!$pagoId)
                        Nuevo Pago Manual
                    @elseif ($estado === 'pagado')
                        Editar Pago
                    @else
                        Registrar cobro de cuota
                    @endif
                </h3>
                @if ($pagoId)
                    <p class="text-xs text-blue-600 mt-0.5">Comprobante N° {{ str_pad($pagoId, 6, '0', STR_PAD_LEFT) }}</p>
                @endif
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contrato *</label>
                    <select wire:model.live="contratoId" {{ $pagoId ? 'disabled' : '' }} class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contratoId') border-red-400 @enderror {{ $pagoId ? 'bg-gray-50 text-gray-500' : '' }}">
                        <option value="">Seleccionar contrato...</option>
                        @foreach($contratos as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->inquilino->nombre_completo }} — {{ $c->propiedad->direccion_completa }}
                                ($ {{ number_format($c->monto_alquiler, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('contratoId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período Mes *</label>
                        <select wire:model="periodoMes" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                                <option value="{{ $i + 1 }}">{{ $mes }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período Año *</label>
                        <input type="number" wire:model="periodoAnio" min="2020" max="2030"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vencimiento *</label>
                        <input type="date" wire:model="fechaVencimiento" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Pago</label>
                        <input type="date" wire:model="fechaPago" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Aviso: gastos auto-aplicados del período --}}
                @if(count($gastosAplicadosIds) > 0)
                <div class="bg-green-50 border border-green-200 rounded-xl p-3 flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-green-800">Descuento aplicado automáticamente</p>
                        <p class="text-xs text-green-700">
                            Se {{ count($gastosAplicadosIds) === 1 ? 'encontró 1 gasto' : 'encontraron ' . count($gastosAplicadosIds) . ' gastos' }}
                            del período y {{ count($gastosAplicadosIds) === 1 ? 'fue aplicado' : 'fueron aplicados' }} como descuento.
                        </p>
                    </div>
                </div>
                @endif

                {{-- Gastos disponibles del inmueble --}}
                @if(count($gastosDisponibles) > 0)
                <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
                    <p class="text-xs font-semibold text-orange-700 mb-2">
                        Gastos del inmueble — clic para cambiar el descuento
                    </p>
                    <div class="space-y-1">
                        @foreach($gastosDisponibles as $g)
                        <button type="button" wire:click="aplicarGasto({{ $g['id'] }})"
                            class="w-full flex items-center justify-between px-3 py-1.5 bg-white border rounded-lg transition-colors text-xs
                                {{ in_array($g['id'], $gastosAplicadosIds) ? 'border-green-400 bg-green-50' : 'border-orange-200 hover:bg-orange-100' }}">
                            <span class="text-gray-700">{{ ucfirst($g['concepto']) }} <span class="text-gray-400">— {{ $g['categoria'] }}</span></span>
                            <div class="flex items-center gap-2">
                                @if(in_array($g['id'], $gastosAplicadosIds))
                                    <span class="text-green-600 text-xs">✓ Aplicado</span>
                                @endif
                                <span class="font-semibold text-orange-600">$ {{ number_format($g['monto'], 0, ',', '.') }}</span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                        <input type="number" wire:model="monto" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('monto') border-red-400 @enderror">
                        @error('monto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recargo</label>
                        <input type="number" wire:model="recargo" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descuento</label>
                        <input type="number" wire:model.blur="descuento" step="0.01"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                @if((float)$descuento > 0)
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría del descuento</label>
                    <select wire:model="descuentoCategoria" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medio de Pago</label>
                        <select wire:model="medioPago" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model="estado" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pagado">Pagado</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                <button wire:click="guardar" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Guardar Pago
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

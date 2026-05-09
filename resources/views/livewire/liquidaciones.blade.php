<div>
    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Emitidas / Pendientes</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalPendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pagadas</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalPagadas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Monto Pendiente</p>
            <p class="text-3xl font-bold text-red-600 mt-1">$ {{ number_format($montoPendiente, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input wire:model.live="busqueda" type="text" placeholder="Buscar propietario o inmueble..."
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 col-span-2">

            <select wire:model.live="filtroEstado" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="emitida">Emitida</option>
                <option value="pagada">Pagada</option>
            </select>

            <select wire:model.live="filtroMes" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="0">Todos los meses</option>
                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                    <option value="{{ $i + 1 }}">{{ $mes }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Liquidaciones</h3>
            <button wire:click="abrirNueva"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Liquidación
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">N°</th>
                        <th class="px-4 py-3 text-left">Período</th>
                        <th class="px-4 py-3 text-left">Propietario</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-right">Alquiler</th>
                        <th class="px-4 py-3 text-right">Comisión</th>
                        <th class="px-4 py-3 text-right">Gastos</th>
                        <th class="px-4 py-3 text-right font-bold">Neto</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($liquidaciones as $liq)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                            {{ str_pad($liq->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $liq->periodo_label }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $liq->propietario?->nombre_completo ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">
                            {{ $liq->propiedad->direccion_completa }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">
                            $ {{ number_format($liq->monto_alquiler, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-red-600">
                            - $ {{ number_format($liq->monto_comision, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-orange-600">
                            @if($liq->total_gastos > 0)
                                - $ {{ number_format($liq->total_gastos, 0, ',', '.') }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            $ {{ number_format($liq->monto_neto, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $liq->estado_badge }}">
                                {{ $liq->estado_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Cambiar estado --}}
                                <button wire:click="abrirModal({{ $liq->id }})"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar estado">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- PDF --}}
                                <a href="{{ route('liquidaciones.pdf', $liq->id) }}"
                                    target="_blank"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Descargar PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                            No hay liquidaciones que coincidan con los filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($liquidaciones->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $liquidaciones->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Editar Estado --}}
    @if($modalAbrir)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Actualizar Liquidación</h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="estado" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="emitida">Emitida</option>
                        <option value="pagada">Pagada</option>
                    </select>
                </div>

                @if($estado === 'pagada')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de pago al propietario</label>
                    <input type="date" wire:model="fechaPagoPropietario"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medio de pago</label>
                    <select wire:model="medioPago" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="cheque">Cheque</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Notas adicionales..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </button>
                <button wire:click="cambiarEstado"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Nueva Liquidación --}}
    @if ($modalNueva)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Nueva Liquidación a Propietario</h3>
                    <button wire:click="cerrarModalNueva" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="guardarNueva" class="px-6 py-5 space-y-4">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Contrato *</label>
                        <select wire:model.live="nuevaContratoId"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nuevaContratoId') border-red-400 @enderror">
                            <option value="">— Seleccioná un contrato —</option>
                            @foreach ($contratosActivos as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->inquilino->nombre_completo }} —
                                    {{ $c->propiedad->direccion_completa }}
                                    {{ $c->propiedad->propietario ? '(Prop: ' . $c->propiedad->propietario->apellido . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('nuevaContratoId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Mes *</label>
                            <select wire:model="nuevaMes"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $mes)
                                    <option value="{{ $i + 1 }}">{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Año *</label>
                            <input wire:model="nuevaAnio" type="number" min="2020"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Monto alquiler *</label>
                        <input wire:model.live="nuevaMontoAlquiler" type="number" step="0.01"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nuevaMontoAlquiler') border-red-400 @enderror">
                        @error('nuevaMontoAlquiler') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Descuento: toggle porcentaje / valor --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Descuento (comisión)</label>
                        <div class="flex rounded-lg border border-gray-200 overflow-hidden mb-3">
                            <button type="button" wire:click="$set('nuevaDescuentoTipo','porcentaje')"
                                class="flex-1 py-1.5 text-xs font-medium transition-colors
                                    {{ $nuevaDescuentoTipo === 'porcentaje' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                % Porcentaje
                            </button>
                            <button type="button" wire:click="$set('nuevaDescuentoTipo','valor')"
                                class="flex-1 py-1.5 text-xs font-medium transition-colors border-l border-gray-200
                                    {{ $nuevaDescuentoTipo === 'valor' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                $ Valor fijo
                            </button>
                        </div>

                        @if ($nuevaDescuentoTipo === 'porcentaje')
                            <input wire:model.live="nuevaComisionPorcentaje" type="number" step="0.01" min="0" max="100"
                                placeholder="Ej: 10"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @else
                            <div class="flex gap-2">
                                <select wire:model.live="nuevaDescuentoMoneda"
                                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="ARS">$ Pesos</option>
                                    <option value="USD">U$S Dólares</option>
                                </select>
                                <input wire:model.live="nuevaDescuentoValorFijo" type="number" step="0.01" min="0"
                                    placeholder="Monto a descontar"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nuevaDescuentoValorFijo') border-red-400 @enderror">
                            </div>
                            @error('nuevaDescuentoValorFijo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    {{-- Gastos deducibles --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Gastos deducibles</label>

                        {{-- Lista de gastos agregados --}}
                        @if(count($nuevosGastos) > 0)
                        <div class="mb-2 space-y-1">
                            @foreach($nuevosGastos as $i => $g)
                            <div class="flex items-center justify-between bg-red-50 border border-red-100 rounded-lg px-3 py-1.5 text-xs">
                                <span class="text-gray-700">{{ $g['categoria'] }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-red-600">- $ {{ number_format($g['monto'], 0, ',', '.') }}</span>
                                    <button type="button" wire:click="quitarGasto({{ $i }})" class="text-gray-400 hover:text-red-500 font-bold leading-none">×</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Fila para agregar gasto --}}
                        <div class="flex gap-2">
                            <select wire:model="gastoCategoria"
                                class="flex-1 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            <input wire:model="gastoMonto" type="number" min="0" step="1" placeholder="Monto"
                                class="w-28 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" wire:click="agregarGasto"
                                class="px-3 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors whitespace-nowrap">
                                + Agregar
                            </button>
                        </div>
                    </div>

                    {{-- Resumen calculado --}}
                    @if ($nuevaMontoAlquiler)
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Alquiler bruto</span>
                                <span>$ {{ number_format((float)$nuevaMontoAlquiler, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                @if ($nuevaDescuentoTipo === 'porcentaje')
                                    <span>Comisión ({{ $nuevaComisionPorcentaje }}%)</span>
                                    <span>- $ {{ number_format($nuevaMontoComision, 0, ',', '.') }}</span>
                                @else
                                    <span>Descuento</span>
                                    <span>- {{ $nuevaDescuentoMoneda === 'USD' ? 'U$S' : '$' }} {{ number_format($nuevaMontoComision, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            @foreach($nuevosGastos as $g)
                            <div class="flex justify-between text-red-600">
                                <span>{{ $g['categoria'] }}</span>
                                <span>- $ {{ number_format($g['monto'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            <div class="flex justify-between font-bold text-blue-700 border-t border-blue-200 pt-2">
                                <span>Monto neto al propietario</span>
                                <span>$ {{ number_format($nuevaMontoNeto, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Observaciones</label>
                        <textarea wire:model="nuevaObservaciones" rows="2"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <button type="button" wire:click="cerrarModalNueva"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            Crear liquidación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

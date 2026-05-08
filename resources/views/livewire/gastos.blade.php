<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input wire:model.live="busqueda" type="text" placeholder="Buscar por inquilino o propietario..."
            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select wire:model.live="filtroCategoria" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>

        <button wire:click="nuevo"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Gasto
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 flex items-center justify-between">
        <span class="text-sm text-gray-500">Total gastos filtrados:</span>
        <span class="font-bold text-gray-900 text-lg">$ {{ number_format($totalGastos, 0, ',', '.') }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Inmueble</th>
                        <th class="px-4 py-3 text-left">Concepto</th>
                        <th class="px-4 py-3 text-left">Categoría</th>
                        <th class="px-4 py-3 text-left">Proveedor</th>
                        <th class="px-4 py-3 text-left">Comprobante</th>
                        <th class="px-4 py-3 text-center">Deducible</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($gastos as $g)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-600">{{ $g->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="text-xs font-medium text-gray-900">{{ $g->propiedad->direccion_completa }}</p>
                            <p class="text-xs text-gray-400">{{ $g->propiedad->propietario->nombre_completo }}</p>
                            @php $inquilino = $g->propiedad->contratos->first()?->inquilino; @endphp
                            @if ($inquilino)
                                <p class="text-xs text-blue-500">Inq: {{ $inquilino->nombre_completo }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($g->concepto) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($g->categoria_label) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $g->proveedor ? ucfirst($g->proveedor) : '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $g->comprobante ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($g->deducible)
                                <span class="text-green-600 font-medium text-xs">Sí</span>
                            @else
                                <span class="text-gray-400 text-xs">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            $ {{ number_format($g->monto, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="editar({{ $g->id }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">No hay gastos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gastos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $gastos->links() }}</div>
        @endif
    </div>

    @if($modalAbrir)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h3 class="font-semibold text-gray-800">{{ $gastoId ? 'Editar Gasto' : 'Nuevo Gasto' }}</h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Propiedad *</label>
                    <select wire:model="propiedadId" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('propiedadId') border-red-400 @enderror">
                        <option value="">Seleccionar propiedad...</option>
                        @foreach($propiedades as $prop)
                            <option value="{{ $prop->id }}">
                                {{ $prop->tipo_label }} — {{ $prop->direccion_completa }} ({{ $prop->propietario->nombre_completo }})
                            </option>
                        @endforeach
                    </select>
                    @error('propiedadId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Concepto *</label>
                    <input type="text" wire:model="concepto" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('concepto') border-red-400 @enderror">
                    @error('concepto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                        <select wire:model="categoria" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" wire:model="fecha" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                        <input type="number" wire:model="monto" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('monto') border-red-400 @enderror">
                        @error('monto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <input type="text" wire:model="proveedor" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° Comprobante</label>
                    <input type="text" wire:model="comprobante" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="deducible" id="deducible" class="rounded border-gray-300">
                    <label for="deducible" class="text-sm text-gray-700">Gasto deducible de la liquidación</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                <button wire:click="guardar" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $gastoId ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

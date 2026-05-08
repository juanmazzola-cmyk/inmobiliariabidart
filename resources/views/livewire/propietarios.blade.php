<div>
    {{-- Barra de herramientas --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <input wire:model.live="busqueda" type="text" placeholder="Buscar por nombre, apellido, DNI o email..."
            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 max-w-md">
        <button wire:click="nuevo"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Propietario
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">DNI / CUIT</th>
                        <th class="px-4 py-3 text-left">Contacto</th>
                        <th class="px-4 py-3 text-left">Ciudad</th>
                        <th class="px-4 py-3 text-center">Propiedades</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($propietarios as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $p->apellido }}, {{ $p->nombre }}</p>
                            @if($p->banco) <p class="text-xs text-gray-400">{{ $p->banco }}</p> @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700">{{ $p->dni }}</p>
                            @if($p->cuit) <p class="text-xs text-gray-400">{{ $p->cuit }}</p> @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($p->email) <p class="text-gray-700">{{ $p->email }}</p> @endif
                            @if($p->telefono) <p class="text-xs text-gray-400">+54 {{ $p->telefono }}</p> @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p->ciudad ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('propiedades.index') }}?filtroPropietario={{ $p->id }}"
                                   class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold hover:bg-blue-200 transition-colors"
                                   title="Ver alquileres">
                                    {{ $p->propiedades_count }} alq.
                                </a>
                                <a href="{{ route('propiedades-venta.index') }}?filtroPropietario={{ $p->id }}"
                                   class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold hover:bg-indigo-200 transition-colors"
                                   title="Ver en venta">
                                    {{ $p->propiedades_venta_count }} vta.
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActivo({{ $p->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $p->activo ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                                {{ $p->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Nueva propiedad en alquiler --}}
                                <a href="{{ route('propiedades.index') }}?abrirConPropietario={{ $p->id }}"
                                    class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-lg transition-colors" title="Agregar casa en alquiler">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </a>
                                {{-- Nueva propiedad en venta --}}
                                <a href="{{ route('propiedades-venta.index') }}?abrirConPropietario={{ $p->id }}"
                                    class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Agregar casa en venta">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </a>
                                {{-- Editar propietario --}}
                                <button wire:click="editar({{ $p->id }})"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            No se encontraron propietarios.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($propietarios->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $propietarios->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($modalAbrir)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h3 class="font-semibold text-gray-800">
                    {{ $propietarioId ? 'Editar Propietario' : 'Nuevo Propietario' }}
                </h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" wire:model="nombre" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-400 @enderror">
                    @error('nombre') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
                    <input type="text" wire:model="apellido" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('apellido') border-red-400 @enderror">
                    @error('apellido') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                    <input type="text" wire:model="dni" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dni') border-red-400 @enderror">
                    @error('dni') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CUIT</label>
                    <input type="text" wire:model="cuit" placeholder="20-12345678-9" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm select-none">+54</span>
                        <input type="text" wire:model="telefono" placeholder="9 11 1234-5678" class="flex-1 border border-gray-200 rounded-r-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" wire:model="direccion" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" wire:model="ciudad" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
                    <input type="text" wire:model="provincia" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CBU</label>
                    <input type="text" wire:model="cbu" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                    <input type="text" wire:model="banco" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <input type="checkbox" wire:model="activo" id="activo" class="rounded border-gray-300">
                    <label for="activo" class="text-sm text-gray-700">Propietario activo</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">Cancelar</button>
                <button wire:click="guardar" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $propietarioId ? 'Actualizar' : 'Crear' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

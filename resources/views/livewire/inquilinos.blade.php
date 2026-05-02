<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <input wire:model.live="busqueda" type="text" placeholder="Buscar por nombre, apellido, DNI o email..."
            class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 max-w-md">
        <button wire:click="nuevo"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Inquilino
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">DNI</th>
                        <th class="px-4 py-3 text-left">Contacto</th>
                        <th class="px-4 py-3 text-left">Ocupación</th>
                        <th class="px-4 py-3 text-left">Garante</th>
                        <th class="px-4 py-3 text-center">Contratos</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($inquilinos as $i)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $i->apellido }}, {{ $i->nombre }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $i->dni }}</td>
                        <td class="px-4 py-3">
                            @if($i->email) <p class="text-gray-700">{{ $i->email }}</p> @endif
                            @if($i->telefono) <p class="text-xs text-gray-400">+54 {{ $i->telefono }}</p> @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $i->ocupacion ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($i->garante_nombre)
                                <p class="text-gray-700 text-xs">{{ $i->garante_nombre }}</p>
                                <p class="text-xs text-gray-400">DNI: {{ $i->garante_dni }}</p>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                {{ $i->contratos_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="editar({{ $i->id }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">No se encontraron inquilinos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquilinos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $inquilinos->links() }}</div>
        @endif
    </div>

    @if($modalAbrir)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
                <h3 class="font-semibold text-gray-800">{{ $inquilinoId ? 'Editar Inquilino' : 'Nuevo Inquilino' }}</h3>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ocupación</label>
                    <input type="text" wire:model="ocupacion" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Datos del Garante</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del garante</label>
                    <input type="text" wire:model="garanteNombre" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI del garante</label>
                    <input type="text" wire:model="garanteDni" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono del garante</label>
                    <input type="text" wire:model="garanteTelefono" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                <button wire:click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</button>
                <button wire:click="guardar" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $inquilinoId ? 'Actualizar' : 'Crear' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

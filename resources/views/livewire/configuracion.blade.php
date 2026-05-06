<div class="max-w-2xl">

    <form wire:submit="guardar" class="space-y-6">

        {{-- Logo --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Logo de la inmobiliaria</h3>

            <div class="flex items-start gap-6">
                {{-- Preview actual --}}
                <div class="w-32 h-32 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50 overflow-hidden shrink-0">
                    @if ($nuevoLogo)
                        <img src="{{ $nuevoLogo->temporaryUrl() }}" class="w-full h-full object-contain p-1">
                    @elseif ($logoActual)
                        <img src="{{ asset('storage/' . $logoActual) }}" class="w-full h-full object-contain p-1">
                    @else
                        <div class="text-center text-gray-300">
                            <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs mt-1">Sin logo</p>
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-blue-600 hover:text-blue-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        {{ $logoActual || $nuevoLogo ? 'Cambiar logo' : 'Subir logo' }}
                        <input wire:model="nuevoLogo" type="file" accept="image/*" class="hidden">
                    </label>

                    @if ($logoActual && !$nuevoLogo)
                        <button type="button" wire:click="eliminarLogo"
                            wire:confirm="¿Eliminar el logo actual?"
                            class="flex items-center gap-1 text-xs text-red-500 hover:text-red-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar logo
                        </button>
                    @endif

                    <p class="text-xs text-gray-400">PNG, JPG o SVG · Máx. 2 MB · Recomendado: fondo transparente (PNG)</p>
                    @error('nuevoLogo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Datos de la inmobiliaria --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Datos de la inmobiliaria</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Razón social</label>
                    <input wire:model="razonSocial" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Matrícula Nacional</label>
                    <input wire:model="cuit" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dirección</label>
                <input wire:model="direccion" type="text"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm select-none">+54</span>
                        <input wire:model="telefono" type="text" placeholder="9 11 1234-5678"
                            class="flex-1 border border-gray-200 rounded-r-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                    <input wire:model="email" type="email"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Guardar configuración
            </button>
        </div>

    </form>

    {{-- Categorías de gastos --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Categorías de gastos</h3>

        <div class="flex gap-2 mb-4">
            <input type="text" wire:model="nuevaCategoria" wire:keydown.enter="agregarCategoria"
                class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Nueva categoría...">
            <button wire:click="agregarCategoria" type="button"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Agregar
            </button>
        </div>
        @error('nuevaCategoria') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

        <div class="space-y-1">
            @foreach ($categorias as $cat)
            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50">
                <span class="text-sm text-gray-700">{{ $cat->nombre }}</span>
                <button wire:click="eliminarCategoria({{ $cat->id }})"
                    wire:confirm="¿Eliminar la categoría '{{ $cat->nombre }}'?"
                    type="button"
                    class="text-gray-300 hover:text-red-500 transition-colors text-xs">
                    ✕
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Acceso al sistema --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mt-6 space-y-4">
        <h3 class="text-sm font-semibold text-gray-700">Acceso al sistema</h3>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Usuario</label>
            <input type="text" wire:model="loginUsuario"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="admin">
            @error('loginUsuario') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Nueva contraseña <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span></label>
            <input type="password" wire:model="loginPassword"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="••••••••">
            @error('loginPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">WhatsApp para recuperación de contraseña</label>
            <div class="flex rounded-lg overflow-hidden border border-gray-200 focus-within:ring-2 focus-within:ring-blue-500">
                <span class="inline-flex items-center px-3 bg-gray-50 text-gray-500 text-sm border-r border-gray-200 select-none">+54</span>
                <input type="text" wire:model="loginWhatsapp"
                    class="flex-1 px-3 py-2 text-sm focus:outline-none"
                    placeholder="9 11 1234-5678">
            </div>
            @error('loginWhatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

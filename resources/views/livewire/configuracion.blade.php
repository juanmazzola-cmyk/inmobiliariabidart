<div class="max-w-2xl">

    <form wire:submit="guardar" class="space-y-6">

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

        {{-- Acceso al sistema --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
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
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="loginPassword"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="••••••••">
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
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

        {{-- Botón guardar --}}
        <button type="submit"
            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors text-sm tracking-wide">
            GUARDAR CONFIGURACIÓN
        </button>

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

</div>

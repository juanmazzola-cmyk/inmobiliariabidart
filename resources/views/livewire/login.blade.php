<div class="w-full max-w-sm"
    x-data
    @abrir-whatsapp.window="window.open($event.detail.url, '_blank')">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">🏠</div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Inmobiliaria</h1>
        <p class="text-slate-400 text-sm mt-1">Panel de administración</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-blue-700 px-8 py-4">
            <h2 class="text-base font-semibold text-white text-center tracking-wide">Iniciar sesión</h2>
        </div>

        <div class="px-8 py-7">
            <form wire:submit="ingresar" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Usuario</label>
                    <input type="text" wire:model="usuario"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="admin" autofocus autocomplete="username">
                    @error('usuario')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Contraseña</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" wire:model="contrasena"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="••••••••" autocomplete="current-password">
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
                    @error('contrasena')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="remember" id="remember"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="text-sm text-gray-500 select-none">Recordarme</label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 active:bg-blue-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                    <span wire:loading.remove wire:target="ingresar">Ingresar</span>
                    <span wire:loading wire:target="ingresar">Verificando...</span>
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-gray-100 text-center">
                @if ($mensajeRecuperacion)
                    <p class="text-xs text-red-500">{{ $mensajeRecuperacion }}</p>
                @else
                    <button wire:click="recuperarContrasena" type="button"
                        class="text-xs text-slate-400 hover:text-blue-600 transition-colors">
                        <span wire:loading.remove wire:target="recuperarContrasena">¿Olvidaste tu contraseña?</span>
                        <span wire:loading wire:target="recuperarContrasena">Enviando por WhatsApp...</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <p class="text-center text-slate-500 text-xs mt-6">{{ now()->format('d/m/Y') }}</p>
</div>

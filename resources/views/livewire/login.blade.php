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
                    <input type="password" wire:model="contrasena"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="••••••••" autocomplete="current-password">
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

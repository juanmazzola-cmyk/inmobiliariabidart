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

    {{-- Copia de seguridad --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5 mt-6">
        <h3 class="text-sm font-semibold text-gray-700">Copia de seguridad</h3>

        {{-- Exportar --}}
        <div class="flex items-center justify-between py-3 border-b border-gray-100">
            <div>
                <p class="text-sm font-medium text-gray-700">Exportar backup</p>
                <p class="text-xs text-gray-400 mt-0.5">Descarga un archivo .sql con toda la base de datos</p>
            </div>
            <a href="{{ route('configuracion.backup') }}"
               class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar
            </a>
        </div>

        {{-- Importar --}}
        <div>
            <p class="text-sm font-medium text-gray-700 mb-1">Importar backup</p>
            <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-3">
                ⚠️ Esto reemplazará <strong>todos los datos actuales</strong>. Asegurate de tener un backup exportado antes de continuar.
            </p>

            <div class="flex items-center gap-3">
                <label class="flex-1">
                    <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500 cursor-pointer hover:border-gray-300 bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="truncate">
                            {{ $archivoBackup ? $archivoBackup->getClientOriginalName() : 'Seleccionar archivo .sql' }}
                        </span>
                    </div>
                    <input wire:model="archivoBackup" type="file" accept=".sql,.txt" class="hidden">
                </label>

                <button type="button"
                    wire:click="importarBackup"
                    wire:confirm="¿Estás seguro? Esto borrará y reemplazará todos los datos actuales con el backup seleccionado."
                    @if(!$archivoBackup) disabled @endif
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                        {{ $archivoBackup
                            ? 'bg-orange-600 text-white hover:bg-orange-700'
                            : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                    Importar
                </button>
            </div>

            @error('archivoBackup')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

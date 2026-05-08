<div x-data="{ fotos: [], indice: -1 }">

    {{-- Chip propietario activo --}}
    @if($filtroPropietario)
    @php $propietarioActivo = $propietarios->firstWhere('id', $filtroPropietario); @endphp
    <div class="flex items-center gap-2 mb-4">
        <span class="text-sm text-gray-500">Filtrando por:</span>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-full">
            {{ $propietarioActivo?->nombre_completo ?? 'Propietario' }}
            <button wire:click="$set('filtroPropietario', '')" class="text-blue-400 hover:text-blue-700 font-bold leading-none">×</button>
        </span>
        <a href="{{ route('propietarios.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Volver a propietarios</a>
    </div>
    @endif

    {{-- Filtros + botón nuevo --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <input wire:model.live="busqueda" type="text" placeholder="Buscar dirección, propietario o inquilino..."
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <select wire:model.live="filtroTipo" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los tipos</option>
                <option value="casa">Casa</option>
                <option value="departamento">Departamento</option>
                <option value="local_comercial">Local Comercial</option>
                <option value="oficina">Oficina</option>
                <option value="terreno">Terreno</option>
                <option value="galpon">Galpón</option>
                <option value="otro">Otro</option>
            </select>
            <select wire:model.live="filtroEstado" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="alquilada">Alquilada</option>
                <option value="en_reparacion">En Reparación</option>
                <option value="inactiva">Inactiva</option>
            </select>
            <div class="ml-auto">
                <button wire:click="nueva"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Propiedad
                </button>
            </div>
        </div>
    </div>

    {{-- Tarjetas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($propiedades as $p)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">

            {{-- Foto principal --}}
            <div class="relative h-44 bg-gray-100 overflow-hidden">
                @if ($p->primera_foto)
                    <img src="{{ asset('storage/' . $p->primera_foto) }}"
                         alt="{{ $p->direccion_completa }}"
                         class="w-full h-full object-cover cursor-pointer"
                         @click="fotos = @js(collect($p->fotos ?? [])->map(fn($f) => asset('storage/'.$f))->values()->all()); indice = 0">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                {{-- Badge estado --}}
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $p->estado === 'alquilada' ? 'bg-green-100 text-green-800' :
                       ($p->estado === 'disponible' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ match($p->estado) {
                        'alquilada'    => 'Alquilada',
                        'disponible'   => 'Disponible',
                        'en_reparacion'=> 'En Reparación',
                        default        => 'Inactiva'
                    } }}
                </span>

                {{-- Contador fotos --}}
                @if (count($p->fotos ?? []) > 1)
                    <span class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-0.5 rounded-full">
                        {{ count($p->fotos) }} fotos
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="p-4">
                <div class="mb-2">
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 mb-1">
                        {{ $p->tipo_label }}
                    </span>
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $p->direccion_completa }}</h3>
                </div>

                <div class="space-y-1 text-xs text-gray-500 mb-3">
                    @if($p->superficie_total)
                    <p>📐 {{ number_format($p->superficie_total, 0) }} m²
                        @if($p->ambientes) · {{ $p->ambientes }} amb. @endif
                        @if($p->banios) · {{ $p->banios }} baño/s @endif
                        @if($p->cochera) · 🚗 cochera @endif
                    </p>
                    @endif
                    @if($p->descripcion)
                    <p class="text-gray-400 line-clamp-2">{{ $p->descripcion }}</p>
                    @endif
                    @if($p->valor_referencia)
                    <p class="text-sm font-semibold text-green-700 mt-1">Valor: $ {{ number_format($p->valor_referencia, 0, ',', '.') }}</p>
                    @endif
                </div>

                {{-- Miniaturas de fotos adicionales --}}
                @if (count($p->fotos ?? []) > 1)
                    <div class="flex gap-1 mb-3">
                        @foreach (array_slice($p->fotos, 1, 4) as $tIdx => $foto)
                            <img src="{{ asset('storage/' . $foto) }}"
                                 class="w-12 h-12 object-cover rounded cursor-pointer border border-gray-200"
                                 @click="fotos = @js(collect($p->fotos ?? [])->map(fn($f) => asset('storage/'.$f))->values()->all()); indice = {{ $tIdx + 1 }}">
                        @endforeach
                        @if (count($p->fotos) > 5)
                            <div class="w-12 h-12 rounded border border-gray-200 bg-gray-100 flex items-center justify-center text-xs text-gray-500 font-medium">
                                +{{ count($p->fotos) - 5 }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                    <a href="{{ route('contratos.index') }}?propiedad={{ $p->id }}"
                        class="text-xs text-blue-600 hover:underline">
                        {{ $p->contratos_count }} contrato/s →
                    </a>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('contratos.index', ['abrirConPropiedad' => $p->id]) }}"
                            title="Nuevo contrato para esta propiedad"
                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                        <button wire:click="editar({{ $p->id }})"
                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="eliminar({{ $p->id }})"
                            wire:confirm="¿Eliminar esta propiedad? Esta acción no se puede deshacer."
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-12 text-center text-gray-400">
            No se encontraron propiedades.
        </div>
        @endforelse
    </div>

    @if($propiedades->hasPages())
    <div class="mt-6">{{ $propiedades->links() }}</div>
    @endif

    {{-- Modal --}}
    @if ($modalAbrir)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $propiedadId ? 'Editar Propiedad' : 'Nueva Propiedad en Alquiler' }}
                </h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="guardar" class="px-6 py-5 space-y-5">

                {{-- Tipo y estado --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipo *</label>
                        <select wire:model="tipo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="casa">Casa</option>
                            <option value="departamento">Departamento</option>
                            <option value="local_comercial">Local Comercial</option>
                            <option value="oficina">Oficina</option>
                            <option value="terreno">Terreno</option>
                            <option value="galpon">Galpón</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Estado *</label>
                        <select wire:model="estado" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="disponible">Disponible</option>
                            <option value="alquilada">Alquilada</option>
                            <option value="en_reparacion">En Reparación</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>

                {{-- Dirección --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Dirección *</label>
                    <input wire:model="direccion" type="text"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('direccion') border-red-400 @enderror">
                    @error('direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Número</label>
                        <input wire:model="numero" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Piso</label>
                        <input wire:model="piso" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dpto.</label>
                        <input wire:model="departamentoLetra" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ciudad *</label>
                        <input wire:model="ciudad" type="text"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('ciudad') border-red-400 @enderror">
                        @error('ciudad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Provincia</label>
                        <input wire:model="provincia" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cód. Postal</label>
                        <input wire:model="codigoPostal" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sup. Total (m²)</label>
                        <input wire:model="superficieTotal" type="number" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sup. Cubierta (m²)</label>
                        <input wire:model="superficieCubierta" type="number" step="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ambientes</label>
                        <input wire:model="ambientes" type="number" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Baños</label>
                        <input wire:model="banios" type="number" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-2 pb-2">
                        <input wire:model="cochera" type="checkbox" id="cochera_alq" class="rounded border-gray-300 text-blue-600">
                        <label for="cochera_alq" class="text-sm text-gray-700">Cochera</label>
                    </div>
                </div>

                {{-- Propietario --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Propietario</label>
                    <select wire:model="propietarioId" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sin propietario asignado</option>
                        @foreach ($propietarios as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                    <textarea wire:model="descripcion" rows="3"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                {{-- Valor de referencia --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Valor de referencia ($)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium select-none">$</span>
                        <input wire:model.blur="valorReferencia" type="text" inputmode="numeric"
                            placeholder="0"
                            class="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Precio orientativo para mostrar en la ficha de la propiedad.</p>
                </div>

                {{-- Fotos --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-2">Fotos</label>

                    @if (count($fotosActuales) > 0)
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach ($fotosActuales as $index => $foto)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $foto) }}"
                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer"
                                         @click="fotos = @js(collect($fotosActuales)->map(fn($f) => asset('storage/'.$f))->values()->all()); indice = {{ $index }}">
                                    <button type="button" wire:click="marcarEliminarFoto({{ $index }})"
                                        class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (count($nuevasFotos) > 0)
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach ($nuevasFotos as $foto)
                                <img src="{{ $foto->temporaryUrl() }}"
                                     class="w-20 h-20 object-cover rounded-lg border-2 border-blue-200">
                            @endforeach
                        </div>
                    @endif

                    <label class="flex items-center gap-2 cursor-pointer text-sm text-blue-600 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar fotos
                        <input wire:model="nuevasFotos" type="file" multiple accept="image/*" class="hidden">
                    </label>
                    @error('nuevasFotos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" wire:click="cerrarModal"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        {{ $propiedadId ? 'Guardar cambios' : 'Cargar propiedad' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Lightbox --}}
    <div x-show="indice >= 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="indice = -1"
         @keydown.arrow-left.window="if(indice > 0) indice--"
         @keydown.arrow-right.window="if(indice < fotos.length - 1) indice++"
         class="fixed inset-0 z-[100] flex flex-col bg-white"
         style="display:none">

        {{-- Barra superior --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 flex-shrink-0">
            <button @click="indice = -1"
                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver
            </button>
            <p x-show="fotos.length > 1" x-text="`${indice + 1} / ${fotos.length}`"
               class="text-gray-400 text-sm select-none"></p>
        </div>

        {{-- Imagen principal --}}
        <div class="flex-1 flex items-center justify-center min-h-0 px-12 py-6 bg-gray-50 relative">
            <button x-show="indice > 0" @click="indice--"
                class="absolute left-3 top-1/2 -translate-y-1/2 bg-white hover:bg-gray-100 text-gray-700 rounded-full w-10 h-10 flex items-center justify-center text-2xl leading-none shadow border border-gray-200 transition-colors">
                ‹
            </button>

            <img :src="fotos[indice]"
                 style="max-width:100%; max-height:100%; width:auto; height:auto;"
                 class="object-contain rounded shadow-sm block">

            <button x-show="indice < fotos.length - 1" @click="indice++"
                class="absolute right-3 top-1/2 -translate-y-1/2 bg-white hover:bg-gray-100 text-gray-700 rounded-full w-10 h-10 flex items-center justify-center text-2xl leading-none shadow border border-gray-200 transition-colors">
                ›
            </button>
        </div>

        {{-- Tira de miniaturas --}}
        <div x-show="fotos.length > 1"
             class="flex gap-2 overflow-x-auto px-5 py-3 border-t border-gray-200 flex-shrink-0 bg-white">
            <template x-for="(foto, i) in fotos" :key="i">
                <img :src="foto" @click="indice = i"
                     :class="i === indice ? 'ring-2 ring-blue-500 opacity-100' : 'opacity-50 hover:opacity-80'"
                     class="w-16 h-16 object-cover rounded cursor-pointer flex-shrink-0 transition-opacity">
            </template>
        </div>
    </div>

</div>

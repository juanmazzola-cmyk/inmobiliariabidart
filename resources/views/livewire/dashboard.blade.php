<div class="space-y-6">

    {{-- Marca de agua --}}
    <div class="fixed pointer-events-none z-0 flex items-center justify-center"
         style="inset: 0; padding-left: 256px; padding-top: 64px;">
        @if ($config->logo_path)
            <img src="{{ asset('storage/' . $config->logo_path) }}"
                 class="max-w-sm max-h-64 object-contain select-none"
                 style="opacity: 0.06;">
        @else
            <svg viewBox="0 0 260 240" fill="none" xmlns="http://www.w3.org/2000/svg"
                 class="w-96 select-none" style="opacity: 0.06; color: #334155;">
                <rect x="18" y="108" width="224" height="132" rx="3" stroke="currentColor" stroke-width="5" fill="none"/>
                <path d="M4 114 L130 14 L256 114" stroke="currentColor" stroke-width="7" stroke-linejoin="round" stroke-linecap="round" fill="none"/>
                <rect x="168" y="34" width="24" height="54" stroke="currentColor" stroke-width="5" fill="none"/>
                <path d="M108 240 L108 168 Q130 150 152 168 L152 240" stroke="currentColor" stroke-width="5" stroke-linejoin="round" fill="none"/>
                <circle cx="112" cy="206" r="4" fill="currentColor"/>
                <rect x="36" y="133" width="52" height="44" rx="3" stroke="currentColor" stroke-width="4.5" fill="none"/>
                <line x1="62" y1="133" x2="62" y2="177" stroke="currentColor" stroke-width="2.5"/>
                <line x1="36" y1="155" x2="88" y2="155" stroke="currentColor" stroke-width="2.5"/>
                <rect x="172" y="133" width="52" height="44" rx="3" stroke="currentColor" stroke-width="4.5" fill="none"/>
                <line x1="198" y1="133" x2="198" y2="177" stroke="currentColor" stroke-width="2.5"/>
                <line x1="172" y1="155" x2="224" y2="155" stroke="currentColor" stroke-width="2.5"/>
                <line x1="0" y1="239" x2="260" y2="239" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            </svg>
        @endif
    </div>

    {{-- KPIs fila 1: contadores --}}
    <div class="relative z-10 grid grid-cols-3 gap-4">
        <a href="{{ route('propiedades.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-orange-200 transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alquileres</p>
                    <p class="text-2xl font-bold text-orange-500 mt-1">{{ $totalPropiedades }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $propiedadesAlquiladas }} alquiladas · {{ $propiedadesDisponibles }} libres</p>
                </div>
                <div class="bg-orange-100 p-2 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('propiedades-venta.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-purple-200 transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">En Venta</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $ventaDisponibles }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $ventaReservadas }} reservadas</p>
                </div>
                <div class="bg-purple-100 p-2 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('contratos.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-green-200 transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Contratos Activos</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $contratosActivos }}</p>
                    @if($contratosVencer > 0)
                    <p class="text-xs text-orange-500 mt-1">⚠ {{ $contratosVencer }} vencen en 60 días</p>
                    @else
                    <p class="text-xs text-gray-400 mt-1">Sin vencimientos próximos</p>
                    @endif
                </div>
                <div class="bg-green-100 p-2 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    {{-- KPIs fila 2: importes --}}
    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('pagos.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-blue-200 transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobranza del Mes</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">$ {{ number_format($cobranzaMes, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->isoFormat('MMMM YYYY') }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl shrink-0 flex items-center justify-center w-12 h-12">
                    <span class="text-blue-600 font-bold text-xl leading-none">$</span>
                </div>
            </div>
        </a>

        <a href="{{ route('pagos.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-red-200 transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Saldo Pendiente del Mes</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">$ {{ number_format($montoPendienteMes, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $pagosPendientesMes }} sin cobrar · {{ now()->isoFormat('MMMM YYYY') }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-xl shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    {{-- Tablas inferiores --}}
    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Alquileres vencidos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Alquileres vencidos</h3>
                <a href="{{ route('pagos.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($alquileresVencidos as $pago)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $pago->contrato->inquilino->nombre_completo }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $pago->contrato->propiedad->tipo_label }} — {{ $pago->periodo_label }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-600">$ {{ number_format($pago->total, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">Vto. {{ $pago->fecha_vencimiento->format('d/m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">No hay alquileres vencidos.</div>
                @endforelse
            </div>
        </div>

        {{-- Contratos próximos a vencer --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Contratos próximos a vencer</h3>
                <a href="{{ route('contratos.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($contratosProximoVencer as $contrato)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $contrato->inquilino->nombre_completo }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $contrato->propiedad->tipo_label }} — {{ $contrato->propiedad->ciudad }}
                        </p>
                        @if($contrato->propiedad->propietario)
                        <p class="text-xs text-gray-400">{{ $contrato->propiedad->propietario->nombre_completo }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold {{ $contrato->fecha_fin->diffInDays(now()) < 30 ? 'text-red-600' : 'text-orange-500' }}">
                            Vence {{ $contrato->fecha_fin->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">No hay contratos próximos a vencer.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

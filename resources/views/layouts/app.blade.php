<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Inmobiliaria') }} — {{ $title ?? 'Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    {{-- Sidebar --}}
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-slate-800 text-white flex flex-col flex-shrink-0">
            @php $config = \App\Models\Configuracion::get(); @endphp
            <div class="px-6 py-5 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    @if($config->logo_path)
                        <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo" class="h-10 w-10 object-contain rounded">
                    @else
                        <span class="text-2xl">🏠</span>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">Inmobiliaria</h1>
                        <p class="text-xs text-slate-400">Panel de administración</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider px-3">Gestión</p>
                </div>

                <a href="{{ route('propietarios.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('propietarios.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Propietarios
                </a>

                <a href="{{ route('inquilinos.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('inquilinos.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Inquilinos
                </a>

                <a href="{{ route('propiedades.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('propiedades.index') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Casas en alquiler
                </a>

                <a href="{{ route('propiedades-venta.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('propiedades-venta.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Casas en venta
                </a>

                <a href="{{ route('contratos.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('contratos.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Contratos de alquiler
                </a>

                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider px-3">Pagos</p>
                </div>

                <a href="{{ route('gastos.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('gastos.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-7-7 7M5 3a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 19V3z"/>
                    </svg>
                    Gastos deducibles
                </a>

                <a href="{{ route('pagos.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('pagos.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Alquileres
                </a>

                <a href="{{ route('liquidaciones.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('liquidaciones.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Propietarios
                </a>

                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider px-3">Reportes</p>
                </div>

                <a href="{{ route('reportes.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reportes.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Reportes
                </a>

                <div class="pt-3 pb-1">
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider px-3">Sistema</p>
                </div>

                <a href="{{ route('configuracion.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('configuracion.*') ? 'bg-slate-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración
                </a>
            </nav>

            <div class="px-4 py-3 border-t border-slate-700">
                <p class="text-xs text-slate-400">{{ now()->format('d/m/Y') }}</p>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500 text-right leading-tight">{{ \App\Models\Configuracion::get()->razon_social ?: \App\Models\Configuracion::get()->nombre }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition-colors">
                            Salir
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 min-h-0 overflow-y-auto p-6">
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>

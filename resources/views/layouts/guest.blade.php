<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Inmobiliaria') }} — {{ $title ?? 'Acceso' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen font-sans flex items-center justify-center px-4" style="background: linear-gradient(135deg, #1e293b 0%, #1e3a5f 50%, #1e293b 100%);">
    {{ $slot }}
    @livewireScripts
</body>
</html>

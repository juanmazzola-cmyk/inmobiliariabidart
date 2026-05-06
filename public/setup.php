<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../inmobiliariabidart_app/vendor/autoload.php';
$app = require_once __DIR__.'/../inmobiliariabidart_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo '<pre style="font-family:monospace;font-size:14px;padding:20px;">';

try {
    $kernel->call('migrate', ['--force' => true]);
    echo "✓ Migraciones OK\n";
} catch (Throwable $e) {
    echo "✗ ERROR migraciones: " . $e->getMessage() . "\n";
}

try {
    $kernel->call('optimize:clear');
    echo "✓ Caché limpiada\n";
} catch (Throwable $e) {
    echo "✗ ERROR caché: " . $e->getMessage() . "\n";
}

// Sincronizar usuario con configuracion
try {
    $db = $app->make('db');
    $config = $db->table('configuracion')->where('id', 1)->first();
    $usuario = $config->login_usuario ?? 'admin';
    $password = \Illuminate\Support\Facades\Hash::make('admin123');
    $db->table('users')->where('id', 1)->update([
        'name'     => $usuario,
        'password' => $password,
    ]);
    echo "✓ Usuario sincronizado: '{$usuario}' / contraseña temporal: admin123\n";
} catch (Throwable $e) {
    echo "✗ ERROR usuario: " . $e->getMessage() . "\n";
}

echo "\n⚠ Borrá este archivo (setup.php) del servidor.\n";
echo '</pre>';

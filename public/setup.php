<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
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

try {
    $db = $app->make('db');
    $config = $db->table('configuracion')->where('id', 1)->first();
    $usuario = $config->login_usuario ?? 'admin';
    $password = \Illuminate\Support\Facades\Hash::make('admin123');
    $db->table('users')->where('id', 1)->update([
        'name'     => $usuario,
        'password' => $password,
    ]);
    echo "✓ Usuario: '{$usuario}' / contraseña: admin123\n";
} catch (Throwable $e) {
    echo "✗ ERROR usuario: " . $e->getMessage() . "\n";
}

// Últimas líneas del log de Laravel
echo "\n--- Últimas líneas del log ---\n";
try {
    $logPath = __DIR__ . '/../storage/logs/laravel.log';
    if (file_exists($logPath)) {
        $lines = file($logPath);
        $last = array_slice($lines, -60);
        foreach ($last as $line) echo htmlspecialchars($line);
    } else {
        echo "Log no encontrado en: $logPath\n";
    }
} catch (Throwable $e) {
    echo "Error leyendo log: " . $e->getMessage() . "\n";
}

echo "\n⚠ Borrá este archivo del servidor.\n";
echo '</pre>';

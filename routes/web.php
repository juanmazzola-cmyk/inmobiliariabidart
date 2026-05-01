<?php

use App\Livewire\Configuracion;
use App\Livewire\Contratos;
use App\Livewire\Dashboard;
use App\Livewire\Gastos;
use App\Livewire\Inquilinos;
use App\Livewire\Liquidaciones;
use App\Livewire\Pagos;
use App\Livewire\Propiedades;
use App\Livewire\PropiedadesVenta;
use App\Livewire\Propietarios;
use App\Livewire\Reportes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(url('/dashboard'));
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::get('/propietarios', Propietarios::class)->name('propietarios.index');
Route::get('/inquilinos', Inquilinos::class)->name('inquilinos.index');
Route::get('/propiedades', Propiedades::class)->name('propiedades.index');
Route::get('/propiedades-venta', PropiedadesVenta::class)->name('propiedades-venta.index');
Route::get('/contratos', Contratos::class)->name('contratos.index');
Route::get('/pagos', Pagos::class)->name('pagos.index');
Route::get('/gastos', Gastos::class)->name('gastos.index');
Route::get('/liquidaciones', Liquidaciones::class)->name('liquidaciones.index');
Route::get('/reportes', Reportes::class)->name('reportes.index');
Route::get('/configuracion', Configuracion::class)->name('configuracion.index');

Route::get('/configuracion/backup', function () {
    $dbName = config('database.connections.mysql.database');

    $out  = "-- =============================================\n";
    $out .= "-- Backup: {$dbName}\n";
    $out .= "-- Generado: " . now()->format('d/m/Y H:i:s') . "\n";
    $out .= "-- =============================================\n\n";
    $out .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $out .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
    $out .= "SET NAMES utf8mb4;\n\n";

    $tables = array_map(
        fn($r) => array_values((array) $r)[0],
        DB::select('SHOW TABLES')
    );

    foreach ($tables as $table) {
        $out .= "\n-- Tabla: `{$table}`\n";
        $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
        $out .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $out .= array_values((array) $createRow[0])[1] . ";\n\n";

        $rows = DB::table($table)->get();
        foreach ($rows as $row) {
            $arr  = (array) $row;
            $cols = '`' . implode('`, `', array_keys($arr)) . '`';
            $vals = array_map(function ($v) {
                if ($v === null) return 'NULL';
                $v = str_replace('\\', '\\\\', (string) $v);
                $v = str_replace("'",  "\\'",  $v);
                $v = str_replace("\n", '\\n',  $v);
                $v = str_replace("\r", '\\r',  $v);
                return "'{$v}'";
            }, array_values($arr));
            $out .= "INSERT INTO `{$table}` ({$cols}) VALUES (" . implode(', ', $vals) . ");\n";
        }
        if ($rows->count()) $out .= "\n";
    }

    $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

    $filename = 'backup_inmobiliaria_' . now()->format('Y-m-d_His') . '.sql';

    return response($out, 200, [
        'Content-Type'        => 'application/octet-stream',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ]);
})->name('configuracion.backup');

Route::get('/pagos/{id}/recibo', function (int $id) {
    $pago = \App\Models\Pago::with([
        'contrato.propiedad.propietario',
        'contrato.inquilino',
    ])->findOrFail($id);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.recibo', compact('pago'))
        ->setPaper('a4', 'portrait');

    $nombre = 'recibo_' . str_pad($pago->id, 6, '0', STR_PAD_LEFT)
        . '_' . $pago->periodo_mes . '_' . $pago->periodo_anio . '.pdf';

    return response()->streamDownload(
        fn() => print($pdf->output()),
        $nombre,
        ['Content-Type' => 'application/pdf']
    );
})->name('pagos.recibo');

Route::get('/liquidaciones/{id}/pdf', function (int $id) {
    $component = new Liquidaciones();
    return $component->generarPdf($id);
})->name('liquidaciones.pdf');

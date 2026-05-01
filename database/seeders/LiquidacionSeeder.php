<?php

namespace Database\Seeders;

use App\Models\Liquidacion;
use Illuminate\Database\Seeder;

class LiquidacionSeeder extends Seeder
{
    public function run(): void
    {
        $liquidaciones = [
            // Propietario 1 - Contrato 1 - Depto Santa Fe
            ['propietario_id' => 1, 'propiedad_id' => 1, 'contrato_id' => 1, 'periodo_mes' => 1, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-01-12', 'monto_alquiler' => 180000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 18000.00, 'total_gastos' => 40000.00, 'monto_neto' => 122000.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-01-15', 'medio_pago' => 'transferencia'],
            ['propietario_id' => 1, 'propiedad_id' => 1, 'contrato_id' => 1, 'periodo_mes' => 2, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-02-12', 'monto_alquiler' => 180000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 18000.00, 'total_gastos' => 27000.00, 'monto_neto' => 135000.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-02-14', 'medio_pago' => 'transferencia'],
            ['propietario_id' => 1, 'propiedad_id' => 1, 'contrato_id' => 1, 'periodo_mes' => 3, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-03-16', 'monto_alquiler' => 189000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 18900.00, 'total_gastos' => 45000.00, 'monto_neto' => 125100.00, 'estado' => 'emitida', 'fecha_pago_propietario' => null, 'medio_pago' => null],
            ['propietario_id' => 1, 'propiedad_id' => 1, 'contrato_id' => 1, 'periodo_mes' => 4, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-04-10', 'monto_alquiler' => 180000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 18000.00, 'total_gastos' => 0.00, 'monto_neto' => 162000.00, 'estado' => 'emitida', 'fecha_pago_propietario' => null, 'medio_pago' => null],

            // Propietario 1 - Contrato 2 - Local Cabildo
            ['propietario_id' => 1, 'propiedad_id' => 2, 'contrato_id' => 2, 'periodo_mes' => 1, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-01-05', 'monto_alquiler' => 350000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 35000.00, 'total_gastos' => 32000.00, 'monto_neto' => 283000.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-01-08', 'medio_pago' => 'transferencia'],
            ['propietario_id' => 1, 'propiedad_id' => 2, 'contrato_id' => 2, 'periodo_mes' => 2, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-02-06', 'monto_alquiler' => 350000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 35000.00, 'total_gastos' => 18000.00, 'monto_neto' => 297000.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-02-09', 'medio_pago' => 'transferencia'],
            ['propietario_id' => 1, 'propiedad_id' => 2, 'contrato_id' => 2, 'periodo_mes' => 3, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-03-06', 'monto_alquiler' => 350000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 35000.00, 'total_gastos' => 0.00, 'monto_neto' => 315000.00, 'estado' => 'emitida', 'fecha_pago_propietario' => null, 'medio_pago' => null],
            ['propietario_id' => 1, 'propiedad_id' => 2, 'contrato_id' => 2, 'periodo_mes' => 4, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-04-07', 'monto_alquiler' => 350000.00, 'comision_porcentaje' => 10.00, 'monto_comision' => 35000.00, 'total_gastos' => 0.00, 'monto_neto' => 315000.00, 'estado' => 'emitida', 'fecha_pago_propietario' => null, 'medio_pago' => null],

            // Propietario 2 - Contrato 3 - Casa Rivadavia
            ['propietario_id' => 2, 'propiedad_id' => 3, 'contrato_id' => 3, 'periodo_mes' => 1, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-01-13', 'monto_alquiler' => 250000.00, 'comision_porcentaje' => 8.00, 'monto_comision' => 20000.00, 'total_gastos' => 85000.00, 'monto_neto' => 145000.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-01-16', 'medio_pago' => 'transferencia'],
            ['propietario_id' => 2, 'propiedad_id' => 3, 'contrato_id' => 3, 'periodo_mes' => 2, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-02-19', 'monto_alquiler' => 262500.00, 'comision_porcentaje' => 8.00, 'monto_comision' => 21000.00, 'total_gastos' => 22000.00, 'monto_neto' => 219500.00, 'estado' => 'pagada', 'fecha_pago_propietario' => '2026-02-21', 'medio_pago' => 'efectivo'],
            ['propietario_id' => 2, 'propiedad_id' => 3, 'contrato_id' => 3, 'periodo_mes' => 3, 'periodo_anio' => 2026, 'fecha_liquidacion' => '2026-03-15', 'monto_alquiler' => 250000.00, 'comision_porcentaje' => 8.00, 'monto_comision' => 20000.00, 'total_gastos' => 0.00, 'monto_neto' => 230000.00, 'estado' => 'borrador', 'fecha_pago_propietario' => null, 'medio_pago' => null],
        ];

        foreach ($liquidaciones as $data) {
            Liquidacion::create($data);
        }
    }
}

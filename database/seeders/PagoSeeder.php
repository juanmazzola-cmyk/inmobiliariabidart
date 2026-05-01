<?php

namespace Database\Seeders;

use App\Models\Pago;
use Illuminate\Database\Seeder;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $pagos = [
            // Contrato 1 - Depto Av. Santa Fe - García
            ['contrato_id' => 1, 'fecha_vencimiento' => '2026-01-10', 'fecha_pago' => '2026-01-08', 'periodo_mes' => 1, 'periodo_anio' => 2026, 'monto' => 180000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 180000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-001', 'estado' => 'pagado'],
            ['contrato_id' => 1, 'fecha_vencimiento' => '2026-02-10', 'fecha_pago' => '2026-02-10', 'periodo_mes' => 2, 'periodo_anio' => 2026, 'monto' => 180000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 180000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-002', 'estado' => 'pagado'],
            ['contrato_id' => 1, 'fecha_vencimiento' => '2026-03-10', 'fecha_pago' => '2026-03-15', 'periodo_mes' => 3, 'periodo_anio' => 2026, 'monto' => 180000.00, 'recargo' => 9000.00, 'descuento' => 0, 'total' => 189000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pagado'],
            ['contrato_id' => 1, 'fecha_vencimiento' => '2026-04-10', 'fecha_pago' => '2026-04-07', 'periodo_mes' => 4, 'periodo_anio' => 2026, 'monto' => 180000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 180000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-004', 'estado' => 'pagado'],
            ['contrato_id' => 1, 'fecha_vencimiento' => '2026-05-10', 'fecha_pago' => null, 'periodo_mes' => 5, 'periodo_anio' => 2026, 'monto' => 180000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 180000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pendiente'],

            // Contrato 2 - Local Cabildo - Sánchez
            ['contrato_id' => 2, 'fecha_vencimiento' => '2026-01-05', 'fecha_pago' => '2026-01-03', 'periodo_mes' => 1, 'periodo_anio' => 2026, 'monto' => 350000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 350000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-010', 'estado' => 'pagado'],
            ['contrato_id' => 2, 'fecha_vencimiento' => '2026-02-05', 'fecha_pago' => '2026-02-05', 'periodo_mes' => 2, 'periodo_anio' => 2026, 'monto' => 350000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 350000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-011', 'estado' => 'pagado'],
            ['contrato_id' => 2, 'fecha_vencimiento' => '2026-03-05', 'fecha_pago' => '2026-03-05', 'periodo_mes' => 3, 'periodo_anio' => 2026, 'monto' => 350000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 350000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-012', 'estado' => 'pagado'],
            ['contrato_id' => 2, 'fecha_vencimiento' => '2026-04-05', 'fecha_pago' => '2026-04-05', 'periodo_mes' => 4, 'periodo_anio' => 2026, 'monto' => 350000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 350000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-013', 'estado' => 'pagado'],
            ['contrato_id' => 2, 'fecha_vencimiento' => '2026-05-05', 'fecha_pago' => null, 'periodo_mes' => 5, 'periodo_anio' => 2026, 'monto' => 350000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 350000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => null, 'estado' => 'pendiente'],

            // Contrato 3 - Casa Rivadavia - Pérez
            ['contrato_id' => 3, 'fecha_vencimiento' => '2026-01-15', 'fecha_pago' => '2026-01-12', 'periodo_mes' => 1, 'periodo_anio' => 2026, 'monto' => 250000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 250000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pagado'],
            ['contrato_id' => 3, 'fecha_vencimiento' => '2026-02-15', 'fecha_pago' => '2026-02-18', 'periodo_mes' => 2, 'periodo_anio' => 2026, 'monto' => 250000.00, 'recargo' => 12500.00, 'descuento' => 0, 'total' => 262500.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pagado'],
            ['contrato_id' => 3, 'fecha_vencimiento' => '2026-03-15', 'fecha_pago' => '2026-03-14', 'periodo_mes' => 3, 'periodo_anio' => 2026, 'monto' => 250000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 250000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pagado'],
            ['contrato_id' => 3, 'fecha_vencimiento' => '2026-04-15', 'fecha_pago' => null, 'periodo_mes' => 4, 'periodo_anio' => 2026, 'monto' => 250000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 250000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'vencido'],
            ['contrato_id' => 3, 'fecha_vencimiento' => '2026-05-15', 'fecha_pago' => null, 'periodo_mes' => 5, 'periodo_anio' => 2026, 'monto' => 250000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 250000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pendiente'],

            // Contrato 4 - Depto La Plata - Torres
            ['contrato_id' => 4, 'fecha_vencimiento' => '2026-01-10', 'fecha_pago' => '2026-01-09', 'periodo_mes' => 1, 'periodo_anio' => 2026, 'monto' => 120000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 120000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-020', 'estado' => 'pagado'],
            ['contrato_id' => 4, 'fecha_vencimiento' => '2026-02-10', 'fecha_pago' => '2026-02-10', 'periodo_mes' => 2, 'periodo_anio' => 2026, 'monto' => 120000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 120000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-021', 'estado' => 'pagado'],
            ['contrato_id' => 4, 'fecha_vencimiento' => '2026-03-10', 'fecha_pago' => '2026-03-10', 'periodo_mes' => 3, 'periodo_anio' => 2026, 'monto' => 120000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 120000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-022', 'estado' => 'pagado'],
            ['contrato_id' => 4, 'fecha_vencimiento' => '2026-04-10', 'fecha_pago' => '2026-04-10', 'periodo_mes' => 4, 'periodo_anio' => 2026, 'monto' => 120000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 120000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-023', 'estado' => 'pagado'],
            ['contrato_id' => 4, 'fecha_vencimiento' => '2026-05-10', 'fecha_pago' => null, 'periodo_mes' => 5, 'periodo_anio' => 2026, 'monto' => 120000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 120000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pendiente'],

            // Contrato 5 - Oficina Córdoba - Ruiz
            ['contrato_id' => 5, 'fecha_vencimiento' => '2026-01-10', 'fecha_pago' => '2026-01-08', 'periodo_mes' => 1, 'periodo_anio' => 2026, 'monto' => 90000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 90000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-030', 'estado' => 'pagado'],
            ['contrato_id' => 5, 'fecha_vencimiento' => '2026-02-10', 'fecha_pago' => '2026-02-09', 'periodo_mes' => 2, 'periodo_anio' => 2026, 'monto' => 90000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 90000.00, 'medio_pago' => 'transferencia', 'numero_comprobante' => 'TRF-031', 'estado' => 'pagado'],
            ['contrato_id' => 5, 'fecha_vencimiento' => '2026-03-10', 'fecha_pago' => null, 'periodo_mes' => 3, 'periodo_anio' => 2026, 'monto' => 90000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 90000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'vencido'],
            ['contrato_id' => 5, 'fecha_vencimiento' => '2026-04-10', 'fecha_pago' => null, 'periodo_mes' => 4, 'periodo_anio' => 2026, 'monto' => 90000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 90000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'vencido'],
            ['contrato_id' => 5, 'fecha_vencimiento' => '2026-05-10', 'fecha_pago' => null, 'periodo_mes' => 5, 'periodo_anio' => 2026, 'monto' => 90000.00, 'recargo' => 0, 'descuento' => 0, 'total' => 90000.00, 'medio_pago' => 'efectivo', 'numero_comprobante' => null, 'estado' => 'pendiente'],
        ];

        foreach ($pagos as $data) {
            Pago::create($data);
        }
    }
}

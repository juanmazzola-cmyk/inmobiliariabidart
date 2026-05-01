<?php

namespace Database\Seeders;

use App\Models\Gasto;
use Illuminate\Database\Seeder;

class GastoSeeder extends Seeder
{
    public function run(): void
    {
        $gastos = [
            ['propiedad_id' => 1, 'liquidacion_id' => null, 'concepto' => 'Plomería - arreglo canilla', 'categoria' => 'reparacion', 'monto' => 15000.00, 'fecha' => '2026-01-15', 'proveedor' => 'Plomería García', 'comprobante' => 'F-0001', 'deducible' => true],
            ['propiedad_id' => 1, 'liquidacion_id' => null, 'concepto' => 'Expensas ordinarias enero', 'categoria' => 'expensas', 'monto' => 25000.00, 'fecha' => '2026-01-10', 'proveedor' => 'Administración Edificio', 'comprobante' => null, 'deducible' => true],
            ['propiedad_id' => 1, 'liquidacion_id' => null, 'concepto' => 'Expensas ordinarias febrero', 'categoria' => 'expensas', 'monto' => 27000.00, 'fecha' => '2026-02-10', 'proveedor' => 'Administración Edificio', 'comprobante' => null, 'deducible' => true],
            ['propiedad_id' => 1, 'liquidacion_id' => null, 'concepto' => 'Pintura cuarto', 'categoria' => 'reparacion', 'monto' => 45000.00, 'fecha' => '2026-03-05', 'proveedor' => 'Pintores Unidos', 'comprobante' => 'F-0045', 'deducible' => true],
            ['propiedad_id' => 2, 'liquidacion_id' => null, 'concepto' => 'ABL trimestre', 'categoria' => 'impuesto', 'monto' => 32000.00, 'fecha' => '2026-01-20', 'proveedor' => 'AGIP', 'comprobante' => 'BOL-001', 'deducible' => true],
            ['propiedad_id' => 2, 'liquidacion_id' => null, 'concepto' => 'Servicio de limpieza', 'categoria' => 'servicio', 'monto' => 18000.00, 'fecha' => '2026-02-01', 'proveedor' => 'Limpiezas Rápidas', 'comprobante' => null, 'deducible' => false],
            ['propiedad_id' => 3, 'liquidacion_id' => null, 'concepto' => 'Electricidad - tablero nuevo', 'categoria' => 'reparacion', 'monto' => 85000.00, 'fecha' => '2026-01-22', 'proveedor' => 'Electricista Morales', 'comprobante' => 'F-0078', 'deducible' => true],
            ['propiedad_id' => 3, 'liquidacion_id' => null, 'concepto' => 'Impuesto inmobiliario cuota 1', 'categoria' => 'impuesto', 'monto' => 22000.00, 'fecha' => '2026-02-15', 'proveedor' => 'ARBA', 'comprobante' => 'BOL-010', 'deducible' => true],
            ['propiedad_id' => 4, 'liquidacion_id' => null, 'concepto' => 'Expensas enero', 'categoria' => 'expensas', 'monto' => 12000.00, 'fecha' => '2026-01-10', 'proveedor' => 'Consorcio Edificio', 'comprobante' => null, 'deducible' => true],
            ['propiedad_id' => 5, 'liquidacion_id' => null, 'concepto' => 'Expensas enero', 'categoria' => 'expensas', 'monto' => 18000.00, 'fecha' => '2026-01-10', 'proveedor' => 'Administración Torres', 'comprobante' => null, 'deducible' => true],
            ['propiedad_id' => 5, 'liquidacion_id' => null, 'concepto' => 'Aire acondicionado reparación', 'categoria' => 'reparacion', 'monto' => 55000.00, 'fecha' => '2026-02-18', 'proveedor' => 'Frío Total', 'comprobante' => 'F-0092', 'deducible' => true],
        ];

        foreach ($gastos as $data) {
            Gasto::create($data);
        }
    }
}

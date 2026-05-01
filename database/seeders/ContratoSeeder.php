<?php

namespace Database\Seeders;

use App\Models\Contrato;
use Illuminate\Database\Seeder;

class ContratoSeeder extends Seeder
{
    public function run(): void
    {
        $contratos = [
            [
                'propiedad_id' => 1,
                'inquilino_id' => 1,
                'fecha_inicio' => '2024-01-01',
                'fecha_fin' => '2026-12-31',
                'monto_alquiler' => 180000.00,
                'dia_vencimiento' => 10,
                'comision_porcentaje' => 10.00,
                'deposito_garantia' => 180000.00,
                'moneda' => 'ARS',
                'estado' => 'activo',
                'incremento_automatico' => true,
                'porcentaje_incremento' => 30.00,
                'meses_incremento' => 4,
                'clausulas_adicionales' => 'Prohibido subarrendar. Mascotas permitidas con depósito adicional.',
            ],
            [
                'propiedad_id' => 2,
                'inquilino_id' => 2,
                'fecha_inicio' => '2024-03-01',
                'fecha_fin' => '2027-02-28',
                'monto_alquiler' => 350000.00,
                'dia_vencimiento' => 5,
                'comision_porcentaje' => 10.00,
                'deposito_garantia' => 350000.00,
                'moneda' => 'ARS',
                'estado' => 'activo',
                'incremento_automatico' => true,
                'porcentaje_incremento' => 25.00,
                'meses_incremento' => 4,
                'clausulas_adicionales' => 'Local destinado exclusivamente a uso comercial.',
            ],
            [
                'propiedad_id' => 3,
                'inquilino_id' => 3,
                'fecha_inicio' => '2023-06-01',
                'fecha_fin' => '2026-05-31',
                'monto_alquiler' => 250000.00,
                'dia_vencimiento' => 15,
                'comision_porcentaje' => 8.00,
                'deposito_garantia' => 500000.00,
                'moneda' => 'ARS',
                'estado' => 'activo',
                'incremento_automatico' => false,
                'porcentaje_incremento' => null,
                'meses_incremento' => null,
                'clausulas_adicionales' => null,
            ],
            [
                'propiedad_id' => 4,
                'inquilino_id' => 4,
                'fecha_inicio' => '2024-06-01',
                'fecha_fin' => '2026-05-31',
                'monto_alquiler' => 120000.00,
                'dia_vencimiento' => 10,
                'comision_porcentaje' => 10.00,
                'deposito_garantia' => 120000.00,
                'moneda' => 'ARS',
                'estado' => 'activo',
                'incremento_automatico' => true,
                'porcentaje_incremento' => 30.00,
                'meses_incremento' => 4,
                'clausulas_adicionales' => null,
            ],
            [
                'propiedad_id' => 5,
                'inquilino_id' => 5,
                'fecha_inicio' => '2024-09-01',
                'fecha_fin' => '2026-08-31',
                'monto_alquiler' => 90000.00,
                'dia_vencimiento' => 10,
                'comision_porcentaje' => 10.00,
                'deposito_garantia' => 90000.00,
                'moneda' => 'ARS',
                'estado' => 'activo',
                'incremento_automatico' => true,
                'porcentaje_incremento' => 30.00,
                'meses_incremento' => 4,
                'clausulas_adicionales' => null,
            ],
        ];

        foreach ($contratos as $data) {
            Contrato::create($data);
        }
    }
}

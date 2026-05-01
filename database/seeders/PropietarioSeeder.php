<?php

namespace Database\Seeders;

use App\Models\Propietario;
use Illuminate\Database\Seeder;

class PropietarioSeeder extends Seeder
{
    public function run(): void
    {
        $propietarios = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'dni' => '20345678',
                'cuit' => '20-20345678-5',
                'telefono' => '011-4521-3456',
                'email' => 'carlos.rodriguez@gmail.com',
                'direccion' => 'Av. Corrientes 2345',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'cbu' => '0110123450012345678901',
                'banco' => 'Banco Nación',
                'activo' => true,
            ],
            [
                'nombre' => 'María Elena',
                'apellido' => 'Fernández',
                'dni' => '25678901',
                'cuit' => '27-25678901-3',
                'telefono' => '011-4765-8901',
                'email' => 'mfernandez@hotmail.com',
                'direccion' => 'Calle Lavalle 890',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'cbu' => '0070123450071234567890',
                'banco' => 'Banco Galicia',
                'activo' => true,
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Martínez',
                'dni' => '18234567',
                'cuit' => '20-18234567-2',
                'telefono' => '0221-423-5678',
                'email' => 'roberto.martinez@yahoo.com',
                'direccion' => 'Calle 7 N° 1234',
                'ciudad' => 'La Plata',
                'provincia' => 'Buenos Aires',
                'cbu' => '0170123450017234567890',
                'banco' => 'BBVA',
                'activo' => true,
            ],
            [
                'nombre' => 'Susana',
                'apellido' => 'López',
                'dni' => '22789012',
                'cuit' => '27-22789012-8',
                'telefono' => '0351-456-7890',
                'email' => 'susana.lopez@gmail.com',
                'direccion' => 'Bv. San Juan 456',
                'ciudad' => 'Córdoba',
                'provincia' => 'Córdoba',
                'cbu' => null,
                'banco' => null,
                'activo' => true,
            ],
        ];

        foreach ($propietarios as $data) {
            Propietario::create($data);
        }
    }
}

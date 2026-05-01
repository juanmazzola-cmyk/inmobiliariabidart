<?php

namespace Database\Seeders;

use App\Models\Inquilino;
use Illuminate\Database\Seeder;

class InquilinoSeeder extends Seeder
{
    public function run(): void
    {
        $inquilinos = [
            [
                'nombre' => 'Juan Pablo',
                'apellido' => 'García',
                'dni' => '35123456',
                'telefono' => '11-5234-5678',
                'email' => 'jpgarcia@gmail.com',
                'direccion' => 'Mitre 345',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'ocupacion' => 'Empleado',
                'garante_nombre' => 'Pedro García',
                'garante_dni' => '28456789',
                'garante_telefono' => '11-4567-8901',
                'activo' => true,
            ],
            [
                'nombre' => 'Lucía',
                'apellido' => 'Sánchez',
                'dni' => '38456789',
                'telefono' => '11-6345-6789',
                'email' => 'lucia.sanchez@outlook.com',
                'direccion' => 'Roca 678',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'ocupacion' => 'Comerciante',
                'garante_nombre' => null,
                'garante_dni' => null,
                'garante_telefono' => null,
                'activo' => true,
            ],
            [
                'nombre' => 'Diego Ariel',
                'apellido' => 'Pérez',
                'dni' => '32567890',
                'telefono' => '0221-555-4321',
                'email' => 'diego.perez@gmail.com',
                'direccion' => 'Calle 50 N° 789',
                'ciudad' => 'La Plata',
                'provincia' => 'Buenos Aires',
                'ocupacion' => 'Docente',
                'garante_nombre' => 'Ana Pérez',
                'garante_dni' => '29876543',
                'garante_telefono' => '0221-555-9876',
                'activo' => true,
            ],
            [
                'nombre' => 'Valentina',
                'apellido' => 'Torres',
                'dni' => '40234567',
                'telefono' => '0351-444-3210',
                'email' => 'valtorres@gmail.com',
                'direccion' => 'Obispo Trejo 234',
                'ciudad' => 'Córdoba',
                'provincia' => 'Córdoba',
                'ocupacion' => 'Profesional',
                'garante_nombre' => null,
                'garante_dni' => null,
                'garante_telefono' => null,
                'activo' => true,
            ],
            [
                'nombre' => 'Marcos',
                'apellido' => 'Ruiz',
                'dni' => '36789012',
                'telefono' => '11-7456-7890',
                'email' => 'marcos.ruiz@hotmail.com',
                'direccion' => 'Venezuela 1234',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'ocupacion' => 'Ingeniero',
                'garante_nombre' => 'Liliana Ruiz',
                'garante_dni' => '27345678',
                'garante_telefono' => '11-4321-5678',
                'activo' => true,
            ],
        ];

        foreach ($inquilinos as $data) {
            Inquilino::create($data);
        }
    }
}

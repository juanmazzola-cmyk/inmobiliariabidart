<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@inmobiliaria.com',
            'password' => bcrypt('password'),
        ]);

        $this->call([
            PropietarioSeeder::class,
            InquilinoSeeder::class,
            PropiedadSeeder::class,
            ContratoSeeder::class,
            PagoSeeder::class,
            LiquidacionSeeder::class,
            GastoSeeder::class,
            PropiedadesVentaSeeder::class,
        ]);
    }
}

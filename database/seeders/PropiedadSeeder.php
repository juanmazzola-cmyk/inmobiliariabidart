<?php

namespace Database\Seeders;

use App\Models\Propiedad;
use Illuminate\Database\Seeder;

class PropiedadSeeder extends Seeder
{
    public function run(): void
    {
        $propiedades = [
            [
                'propietario_id' => 1,
                'tipo' => 'departamento',
                'direccion' => 'Av. Santa Fe',
                'numero' => '2345',
                'piso' => '3',
                'departamento_letra' => 'B',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'codigo_postal' => '1425',
                'superficie_total' => 65.00,
                'superficie_cubierta' => 65.00,
                'ambientes' => 3,
                'banios' => 1,
                'cochera' => false,
                'descripcion' => 'Departamento luminoso en planta alta, living-comedor, dos dormitorios, cocina equipada y baño completo.',
                'estado' => 'alquilada',
            ],
            [
                'propietario_id' => 1,
                'tipo' => 'local_comercial',
                'direccion' => 'Av. Cabildo',
                'numero' => '890',
                'piso' => null,
                'departamento_letra' => null,
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'codigo_postal' => '1426',
                'superficie_total' => 80.00,
                'superficie_cubierta' => 80.00,
                'ambientes' => 1,
                'banios' => 1,
                'cochera' => false,
                'descripcion' => 'Local comercial en avenida principal, amplio frente, baño.',
                'estado' => 'alquilada',
            ],
            [
                'propietario_id' => 2,
                'tipo' => 'casa',
                'direccion' => 'Rivadavia',
                'numero' => '567',
                'piso' => null,
                'departamento_letra' => null,
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'codigo_postal' => '1002',
                'superficie_total' => 120.00,
                'superficie_cubierta' => 100.00,
                'ambientes' => 5,
                'banios' => 2,
                'cochera' => true,
                'descripcion' => 'Casa con jardín, tres dormitorios, dos baños, garage.',
                'estado' => 'alquilada',
            ],
            [
                'propietario_id' => 3,
                'tipo' => 'departamento',
                'direccion' => 'Calle 13',
                'numero' => '456',
                'piso' => '2',
                'departamento_letra' => 'A',
                'ciudad' => 'La Plata',
                'provincia' => 'Buenos Aires',
                'codigo_postal' => '1900',
                'superficie_total' => 55.00,
                'superficie_cubierta' => 55.00,
                'ambientes' => 2,
                'banios' => 1,
                'cochera' => false,
                'descripcion' => 'Monoambiente amplio con balcón.',
                'estado' => 'alquilada',
            ],
            [
                'propietario_id' => 4,
                'tipo' => 'oficina',
                'direccion' => 'Dean Funes',
                'numero' => '1234',
                'piso' => '4',
                'departamento_letra' => 'D',
                'ciudad' => 'Córdoba',
                'provincia' => 'Córdoba',
                'codigo_postal' => '5000',
                'superficie_total' => 45.00,
                'superficie_cubierta' => 45.00,
                'ambientes' => 2,
                'banios' => 1,
                'cochera' => false,
                'descripcion' => 'Oficina en edificio céntrico, recepción y despacho.',
                'estado' => 'alquilada',
            ],
            [
                'propietario_id' => 2,
                'tipo' => 'departamento',
                'direccion' => 'Tucumán',
                'numero' => '1890',
                'piso' => '6',
                'departamento_letra' => 'C',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'CABA',
                'codigo_postal' => '1050',
                'superficie_total' => 48.00,
                'superficie_cubierta' => 48.00,
                'ambientes' => 2,
                'banios' => 1,
                'cochera' => false,
                'descripcion' => 'Departamento céntrico, dormitorio, living y cocina.',
                'estado' => 'disponible',
            ],
        ];

        foreach ($propiedades as $data) {
            Propiedad::create($data);
        }
    }
}

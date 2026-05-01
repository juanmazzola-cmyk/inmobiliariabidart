<?php

namespace Database\Seeders;

use App\Models\PropiedadVenta;
use App\Models\Propietario;
use Illuminate\Database\Seeder;

class PropiedadesVentaSeeder extends Seeder
{
    public function run(): void
    {
        $propietarios = Propietario::pluck('id')->toArray();

        $propiedades = [
            [
                'tipo' => 'casa', 'direccion' => 'Av. Santa Fe', 'numero' => '2340',
                'ciudad' => 'Buenos Aires', 'provincia' => 'Buenos Aires',
                'superficie_total' => 180, 'superficie_cubierta' => 150,
                'ambientes' => 4, 'banios' => 2, 'cochera' => true,
                'precio_venta' => 185000, 'moneda' => 'USD', 'estado' => 'disponible',
                'descripcion' => 'Amplia casa con jardín, garage doble y quincho. Excelente ubicación a pasos del subte.',
            ],
            [
                'tipo' => 'departamento', 'direccion' => 'Av. Corrientes', 'numero' => '4521', 'piso' => '8', 'departamento_letra' => 'B',
                'ciudad' => 'Buenos Aires', 'provincia' => 'Buenos Aires',
                'superficie_total' => 65, 'superficie_cubierta' => 65,
                'ambientes' => 3, 'banios' => 1, 'cochera' => false,
                'precio_venta' => 92000, 'moneda' => 'USD', 'estado' => 'disponible',
                'descripcion' => 'Departamento luminoso en piso alto, vista panorámica. Edificio con amenities.',
            ],
            [
                'tipo' => 'departamento', 'direccion' => 'Av. Cabildo', 'numero' => '1200', 'piso' => '3', 'departamento_letra' => 'A',
                'ciudad' => 'Buenos Aires', 'provincia' => 'Buenos Aires',
                'superficie_total' => 48, 'superficie_cubierta' => 48,
                'ambientes' => 2, 'banios' => 1, 'cochera' => false,
                'precio_venta' => 68000, 'moneda' => 'USD', 'estado' => 'reservada',
                'descripcion' => 'Monoambiente amplio con balcón. Ideal inversión.',
            ],
            [
                'tipo' => 'casa', 'direccion' => 'Calle Las Heras', 'numero' => '875',
                'ciudad' => 'Córdoba', 'provincia' => 'Córdoba',
                'superficie_total' => 240, 'superficie_cubierta' => 190,
                'ambientes' => 5, 'banios' => 3, 'cochera' => true,
                'precio_venta' => 145000, 'moneda' => 'USD', 'estado' => 'disponible',
                'descripcion' => 'Casa de categoría con pileta, barbacoa y amplio jardín. Barrio privado.',
            ],
            [
                'tipo' => 'local_comercial', 'direccion' => 'Av. 9 de Julio', 'numero' => '550',
                'ciudad' => 'Rosario', 'provincia' => 'Santa Fe',
                'superficie_total' => 120, 'superficie_cubierta' => 120,
                'ambientes' => null, 'banios' => 1, 'cochera' => false,
                'precio_venta' => 3500000, 'moneda' => 'ARS', 'estado' => 'disponible',
                'descripcion' => 'Local en planta baja sobre avenida principal. Frente de 8 metros.',
            ],
            [
                'tipo' => 'terreno', 'direccion' => 'Ruta 8', 'numero' => 'Km 45',
                'ciudad' => 'Luján', 'provincia' => 'Buenos Aires',
                'superficie_total' => 1200, 'superficie_cubierta' => null,
                'ambientes' => null, 'banios' => null, 'cochera' => false,
                'precio_venta' => 55000, 'moneda' => 'USD', 'estado' => 'disponible',
                'descripcion' => 'Terreno plano con todos los servicios. Escritura lista.',
            ],
            [
                'tipo' => 'departamento', 'direccion' => 'Bv. Oroño', 'numero' => '980', 'piso' => '12', 'departamento_letra' => 'C',
                'ciudad' => 'Rosario', 'provincia' => 'Santa Fe',
                'superficie_total' => 90, 'superficie_cubierta' => 90,
                'ambientes' => 3, 'banios' => 2, 'cochera' => true,
                'precio_venta' => 115000, 'moneda' => 'USD', 'estado' => 'vendida',
                'descripcion' => 'Departamento de categoría con vista al río. Amenities completos.',
            ],
            [
                'tipo' => 'oficina', 'direccion' => 'Av. del Libertador', 'numero' => '6200', 'piso' => '5',
                'ciudad' => 'Buenos Aires', 'provincia' => 'Buenos Aires',
                'superficie_total' => 75, 'superficie_cubierta' => 75,
                'ambientes' => 4, 'banios' => 1, 'cochera' => true,
                'precio_venta' => 135000, 'moneda' => 'USD', 'estado' => 'disponible',
                'descripcion' => 'Oficina en edificio corporativo AAA, vigilancia 24hs, sala de reuniones.',
            ],
        ];

        foreach ($propiedades as $i => $datos) {
            $datos['propietario_id'] = count($propietarios) > 0 ? $propietarios[$i % count($propietarios)] : null;
            PropiedadVenta::create($datos);
        }
    }
}

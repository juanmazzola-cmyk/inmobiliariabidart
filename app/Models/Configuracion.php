<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'logo_path',
    ];

    public static function get(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'nombre' => 'Inmobiliaria',
        ]);
    }
}

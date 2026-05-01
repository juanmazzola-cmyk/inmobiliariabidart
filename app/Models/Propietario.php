<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PropiedadVenta;

class Propietario extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'provincia',
        'cbu',
        'banco',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class);
    }

    public function propiedadesVenta(): HasMany
    {
        return $this->hasMany(PropiedadVenta::class);
    }

    public function liquidaciones(): HasMany
    {
        return $this->hasMany(Liquidacion::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return mb_strtoupper("{$this->apellido}, {$this->nombre}");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquilino extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'provincia',
        'ocupacion',
        'garante_nombre',
        'garante_dni',
        'garante_telefono',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return mb_strtoupper("{$this->apellido}, {$this->nombre}");
    }

    public function contratoActivo()
    {
        return $this->contratos()->where('estado', 'activo')->latest()->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Propiedad extends Model
{
    protected $table = 'propiedades';

    protected $fillable = [
        'propietario_id',
        'tipo',
        'direccion',
        'numero',
        'piso',
        'departamento_letra',
        'ciudad',
        'provincia',
        'codigo_postal',
        'superficie_total',
        'superficie_cubierta',
        'ambientes',
        'banios',
        'cochera',
        'descripcion',
        'valor_referencia',
        'fotos',
        'estado',
    ];

    protected $casts = [
        'cochera'            => 'boolean',
        'superficie_total'   => 'decimal:2',
        'superficie_cubierta'=> 'decimal:2',
        'valor_referencia'   => 'decimal:2',
        'fotos'              => 'array',
    ];

    public function getPrimeraFotoAttribute(): ?string
    {
        return ($this->fotos && count($this->fotos) > 0) ? $this->fotos[0] : null;
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function liquidaciones(): HasMany
    {
        return $this->hasMany(Liquidacion::class);
    }

    public function contratoActivo()
    {
        return $this->contratos()->where('estado', 'activo')->latest()->first();
    }

    public function getDireccionCompletaAttribute(): string
    {
        $dir = $this->direccion;
        if ($this->numero) $dir .= ' ' . $this->numero;
        if ($this->piso) $dir .= ' Piso ' . $this->piso;
        if ($this->departamento_letra) $dir .= ' Dto. ' . $this->departamento_letra;
        return $dir . ', ' . $this->ciudad;
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'casa' => 'Casa',
            'departamento' => 'Departamento',
            'local_comercial' => 'Local Comercial',
            'oficina' => 'Oficina',
            'terreno' => 'Terreno',
            'galpon' => 'Galpón',
            default => 'Otro',
        };
    }
}

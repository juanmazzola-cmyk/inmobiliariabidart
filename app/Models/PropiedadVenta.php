<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropiedadVenta extends Model
{
    protected $table = 'propiedades_venta';

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
        'precio_venta',
        'moneda',
        'descripcion',
        'estado',
        'fotos',
        'observaciones',
    ];

    protected $casts = [
        'superficie_total'    => 'decimal:2',
        'superficie_cubierta' => 'decimal:2',
        'precio_venta'        => 'decimal:2',
        'cochera'             => 'boolean',
        'fotos'               => 'array',
    ];

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function getDireccionCompletaAttribute(): string
    {
        $partes = [$this->direccion];
        if ($this->numero) $partes[] = $this->numero;
        if ($this->piso)   $partes[] = 'Piso ' . $this->piso;
        if ($this->departamento_letra) $partes[] = 'Dpto ' . $this->departamento_letra;
        return implode(' ', $partes) . ', ' . $this->ciudad;
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'casa'            => 'Casa',
            'departamento'    => 'Departamento',
            'local_comercial' => 'Local Comercial',
            'oficina'         => 'Oficina',
            'terreno'         => 'Terreno',
            'galpon'          => 'Galpón',
            default           => 'Otro',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'disponible' => 'Disponible',
            'vendida'    => 'Vendida',
            'reservada'  => 'Reservada',
            default      => $this->estado,
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'disponible' => 'bg-blue-100 text-blue-700',
            'vendida'    => 'bg-green-100 text-green-700',
            'reservada'  => 'bg-yellow-100 text-yellow-700',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    public function getPrecioFormateadoAttribute(): string
    {
        $simbolo = $this->moneda === 'USD' ? 'U$S' : '$';
        return $simbolo . ' ' . number_format((float) $this->precio_venta, 0, ',', '.');
    }

    public function getPrimeraFotoAttribute(): ?string
    {
        $fotos = $this->fotos ?? [];
        return count($fotos) > 0 ? $fotos[0] : null;
    }
}

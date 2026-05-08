<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $fillable = [
        'propiedad_id',
        'liquidacion_id',
        'pago_id',
        'concepto',
        'categoria',
        'monto',
        'fecha',
        'proveedor',
        'comprobante',
        'deducible',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'deducible' => 'boolean',
    ];

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function liquidacion(): BelongsTo
    {
        return $this->belongsTo(Liquidacion::class);
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function getCategoriaLabelAttribute(): string
    {
        return $this->categoria;
    }
}

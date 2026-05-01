<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';

    protected $fillable = [
        'propietario_id',
        'propiedad_id',
        'contrato_id',
        'periodo_mes',
        'periodo_anio',
        'fecha_liquidacion',
        'monto_alquiler',
        'comision_porcentaje',
        'monto_comision',
        'total_gastos',
        'monto_neto',
        'estado',
        'fecha_pago_propietario',
        'medio_pago',
        'observaciones',
    ];

    protected $casts = [
        'fecha_liquidacion' => 'date',
        'fecha_pago_propietario' => 'date',
        'monto_alquiler' => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'monto_comision' => 'decimal:2',
        'total_gastos' => 'decimal:2',
        'monto_neto' => 'decimal:2',
    ];

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Propietario::class);
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function getPeriodoLabelAttribute(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return ($meses[$this->periodo_mes] ?? $this->periodo_mes) . ' ' . $this->periodo_anio;
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'borrador' => 'Borrador',
            'emitida' => 'Emitida',
            'pagada' => 'Pagada',
            default => $this->estado,
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'borrador' => 'bg-gray-100 text-gray-800',
            'emitida' => 'bg-yellow-100 text-yellow-800',
            'pagada' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

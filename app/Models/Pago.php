<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'contrato_id',
        'fecha_pago',
        'fecha_vencimiento',
        'periodo_mes',
        'periodo_anio',
        'monto',
        'recargo',
        'descuento',
        'descuento_categoria',
        'total',
        'medio_pago',
        'numero_comprobante',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'fecha_vencimiento' => 'date',
        'monto' => 'decimal:2',
        'recargo' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function getPeriodoLabelAttribute(): string
    {
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        return ($meses[$this->periodo_mes] ?? $this->periodo_mes) . ' ' . substr($this->periodo_anio, -2);
    }

    public function getMedioPagoLabelAttribute(): string
    {
        return match ($this->medio_pago) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            default => 'Otro',
        };
    }
}

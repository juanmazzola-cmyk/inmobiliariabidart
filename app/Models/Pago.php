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
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return ($meses[$this->periodo_mes] ?? $this->periodo_mes) . ' ' . $this->periodo_anio;
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

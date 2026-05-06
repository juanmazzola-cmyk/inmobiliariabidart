<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Contrato extends Model
{
    protected $fillable = [
        'propiedad_id',
        'inquilino_id',
        'fecha_inicio',
        'fecha_fin',
        'monto_alquiler',
        'dia_vencimiento',
        'comision_porcentaje',
        'comision_monto',
        'deposito_garantia',
        'moneda',
        'estado',
        'incremento_automatico',
        'porcentaje_incremento',
        'meses_incremento',
        'clausulas_adicionales',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto_alquiler' => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'comision_monto'      => 'decimal:2',
        'deposito_garantia'   => 'decimal:2',
        'incremento_automatico'  => 'boolean',
        'porcentaje_incremento'  => 'decimal:2',
        'ultimo_incremento_at'   => 'date',
    ];

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function inquilino(): BelongsTo
    {
        return $this->belongsTo(Inquilino::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function liquidaciones(): HasMany
    {
        return $this->hasMany(Liquidacion::class);
    }

    public function getDuracionMesesAttribute(): int
    {
        return $this->fecha_inicio->diffInMonths($this->fecha_fin);
    }

    public function getMesesRestantesAttribute(): int
    {
        if ($this->estado !== 'activo') return 0;
        return max(0, Carbon::now()->diffInMonths($this->fecha_fin, false));
    }

    public function getMontoComisonAttribute(): float
    {
        return round($this->monto_alquiler * $this->comision_porcentaje / 100, 2);
    }

    public function getMontoNetoAttribute(): float
    {
        return round($this->monto_alquiler - $this->monto_comision, 2);
    }

    public function estaVencido(): bool
    {
        return $this->fecha_fin->isPast();
    }
}

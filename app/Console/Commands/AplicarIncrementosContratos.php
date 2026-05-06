<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\Pago;
use Illuminate\Console\Command;

class AplicarIncrementosContratos extends Command
{
    protected $signature   = 'contratos:incrementar';
    protected $description = 'Aplica incrementos automáticos de alquiler a los contratos que corresponda';

    public function handle(): int
    {
        $contratos = Contrato::where('estado', 'activo')
            ->where('incremento_automatico', true)
            ->whereNotNull('porcentaje_incremento')
            ->whereNotNull('meses_incremento')
            ->get();

        $aplicados = 0;

        foreach ($contratos as $contrato) {
            $referencia       = $contrato->ultimo_incremento_at ?? $contrato->fecha_inicio;
            $mesesTranscurridos = $referencia->diffInMonths(now());

            if ($mesesTranscurridos < $contrato->meses_incremento) {
                continue;
            }

            $montoActual = (float) $contrato->monto_alquiler;
            $montoNuevo  = round($montoActual * (1 + $contrato->porcentaje_incremento / 100), 2);

            $contrato->update([
                'monto_alquiler'      => $montoNuevo,
                'ultimo_incremento_at'=> now()->toDateString(),
            ]);

            // Actualizar cuotas pendientes y vencidas
            Pago::where('contrato_id', $contrato->id)
                ->whereIn('estado', ['pendiente', 'vencido'])
                ->update(['monto' => $montoNuevo, 'total' => $montoNuevo]);

            $this->line("✓ Contrato #{$contrato->id}: $ " . number_format($montoActual, 0, ',', '.') . " → $ " . number_format($montoNuevo, 0, ',', '.'));
            $aplicados++;
        }

        $this->info($aplicados > 0 ? "{$aplicados} incremento(s) aplicado(s)." : 'No hay incrementos pendientes.');
        return Command::SUCCESS;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquidaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->constrained('propietarios')->onDelete('restrict');
            $table->foreignId('propiedad_id')->constrained('propiedades')->onDelete('restrict');
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('restrict');
            $table->integer('periodo_mes');
            $table->integer('periodo_anio');
            $table->date('fecha_liquidacion');
            $table->decimal('monto_alquiler', 12, 2);
            $table->decimal('comision_porcentaje', 5, 2);
            $table->decimal('monto_comision', 12, 2);
            $table->decimal('total_gastos', 12, 2)->default(0);
            $table->decimal('monto_neto', 12, 2);
            $table->enum('estado', ['borrador', 'emitida', 'pagada'])->default('borrador');
            $table->date('fecha_pago_propietario')->nullable();
            $table->enum('medio_pago', ['efectivo', 'transferencia', 'cheque', 'otro'])->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['contrato_id', 'periodo_mes', 'periodo_anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidaciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('restrict');
            $table->date('fecha_pago')->nullable();
            $table->date('fecha_vencimiento');
            $table->integer('periodo_mes');
            $table->integer('periodo_anio');
            $table->decimal('monto', 12, 2);
            $table->decimal('recargo', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('medio_pago', ['efectivo', 'transferencia', 'cheque', 'otro'])->default('efectivo');
            $table->string('numero_comprobante')->nullable();
            $table->enum('estado', ['pendiente', 'pagado', 'vencido', 'parcial'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

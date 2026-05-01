<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->constrained('propiedades')->onDelete('restrict');
            $table->foreignId('inquilino_id')->constrained('inquilinos')->onDelete('restrict');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('monto_alquiler', 12, 2);
            $table->integer('dia_vencimiento')->default(10);
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->decimal('deposito_garantia', 12, 2)->default(0);
            $table->string('moneda', 10)->default('ARS');
            $table->enum('estado', ['activo', 'vencido', 'rescindido', 'renovado'])->default('activo');
            $table->boolean('incremento_automatico')->default(false);
            $table->decimal('porcentaje_incremento', 5, 2)->nullable();
            $table->integer('meses_incremento')->nullable();
            $table->text('clausulas_adicionales')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};

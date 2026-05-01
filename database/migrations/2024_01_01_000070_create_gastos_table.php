<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->constrained('propiedades')->onDelete('restrict');
            $table->foreignId('liquidacion_id')->nullable()->constrained('liquidaciones')->nullOnDelete();
            $table->string('concepto');
            $table->enum('categoria', ['reparacion', 'expensas', 'impuesto', 'servicio', 'administracion', 'otro']);
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('proveedor')->nullable();
            $table->string('comprobante')->nullable();
            $table->boolean('deducible')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};

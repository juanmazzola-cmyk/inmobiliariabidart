<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propiedades_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->nullable()->constrained('propietarios')->nullOnDelete();
            $table->enum('tipo', ['casa', 'departamento', 'local_comercial', 'oficina', 'terreno', 'galpon', 'otro'])->default('casa');
            $table->string('direccion');
            $table->string('numero', 20)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('departamento_letra', 5)->nullable();
            $table->string('ciudad');
            $table->string('provincia')->default('Buenos Aires');
            $table->string('codigo_postal', 10)->nullable();
            $table->decimal('superficie_total', 10, 2)->nullable();
            $table->decimal('superficie_cubierta', 10, 2)->nullable();
            $table->unsignedTinyInteger('ambientes')->nullable();
            $table->unsignedTinyInteger('banios')->nullable();
            $table->boolean('cochera')->default(false);
            $table->decimal('precio_venta', 14, 2);
            $table->enum('moneda', ['ARS', 'USD'])->default('USD');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['disponible', 'vendida', 'reservada'])->default('disponible');
            $table->json('fotos')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propiedades_venta');
    }
};

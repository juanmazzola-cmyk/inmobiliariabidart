<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->constrained('propietarios')->onDelete('restrict');
            $table->enum('tipo', ['casa', 'departamento', 'local_comercial', 'oficina', 'terreno', 'galpon', 'otro']);
            $table->string('direccion');
            $table->string('numero', 20)->nullable();
            $table->string('piso', 10)->nullable();
            $table->string('departamento_letra', 10)->nullable();
            $table->string('ciudad');
            $table->string('provincia');
            $table->string('codigo_postal', 10)->nullable();
            $table->decimal('superficie_total', 10, 2)->nullable();
            $table->decimal('superficie_cubierta', 10, 2)->nullable();
            $table->integer('ambientes')->nullable();
            $table->integer('banios')->nullable();
            $table->boolean('cochera')->default(false);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['disponible', 'alquilada', 'en_reparacion', 'inactiva'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};

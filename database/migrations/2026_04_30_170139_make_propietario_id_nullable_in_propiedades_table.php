<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propiedades', function (Blueprint $table) {
            $table->dropForeign(['propietario_id']);
        });

        DB::statement('ALTER TABLE propiedades MODIFY COLUMN propietario_id BIGINT UNSIGNED NULL');

        Schema::table('propiedades', function (Blueprint $table) {
            $table->foreign('propietario_id')
                  ->references('id')
                  ->on('propietarios')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('propiedades', function (Blueprint $table) {
            $table->dropForeign(['propietario_id']);
        });

        DB::statement('ALTER TABLE propiedades MODIFY COLUMN propietario_id BIGINT UNSIGNED NOT NULL');

        Schema::table('propiedades', function (Blueprint $table) {
            $table->foreign('propietario_id')
                  ->references('id')
                  ->on('propietarios')
                  ->onDelete('restrict');
        });
    }
};

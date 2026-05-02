<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // pagos: contrato_id restrict → cascade
        DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_contrato_id_foreign');
        DB::statement('ALTER TABLE pagos ADD CONSTRAINT pagos_contrato_id_foreign FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE CASCADE');

        // liquidaciones: contrato_id restrict → cascade
        DB::statement('ALTER TABLE liquidaciones DROP FOREIGN KEY liquidaciones_contrato_id_foreign');
        DB::statement('ALTER TABLE liquidaciones ADD CONSTRAINT liquidaciones_contrato_id_foreign FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_contrato_id_foreign');
        DB::statement('ALTER TABLE pagos ADD CONSTRAINT pagos_contrato_id_foreign FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE RESTRICT');

        DB::statement('ALTER TABLE liquidaciones DROP FOREIGN KEY liquidaciones_contrato_id_foreign');
        DB::statement('ALTER TABLE liquidaciones ADD CONSTRAINT liquidaciones_contrato_id_foreign FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE RESTRICT');
    }
};

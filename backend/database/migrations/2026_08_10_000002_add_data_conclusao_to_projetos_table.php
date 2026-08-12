<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ProjetoFormModal.vue já coleta "Data de conclusão" (texto livre,
     * ex: "Maio 2026") quando o status é 'concluido', mas a tabela não
     * tinha essa coluna.
     */
    public function up(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->string('data_conclusao')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->dropColumn('data_conclusao');
        });
    }
};

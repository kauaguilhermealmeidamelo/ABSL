<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gestoes_financeiras', function (Blueprint $table) {
            $table->bigInteger('saldo_real_final_centavos')->nullable();
            $table->bigInteger('diferenca_conciliacao_centavos')->nullable();
            $table->text('justificativa_conciliacao')->nullable();
            $table->timestamp('conciliado_em')->nullable();
            $table->foreignId('conciliado_por')->nullable()->constrained('users');
            $table->timestamp('ativada_em')->nullable();
            $table->foreignId('ativada_por')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('gestoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['conciliado_por']);
            $table->dropForeign(['ativada_por']);
            $table->dropColumn(['saldo_real_final_centavos', 'diferenca_conciliacao_centavos', 'justificativa_conciliacao', 'conciliado_em', 'conciliado_por', 'ativada_em', 'ativada_por']);
        });
    }
};

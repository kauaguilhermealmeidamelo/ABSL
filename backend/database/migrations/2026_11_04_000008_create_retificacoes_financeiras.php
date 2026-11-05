<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // A tabela ja existe desde a migration 000001. Acrescentar apenas os campos
        // exigidos pelo fluxo de retificacao, preservando o historico existente.
        Schema::table('retificacoes_financeiras', function (Blueprint $table) {
            $table->bigInteger('valor_anterior_centavos')->nullable();
            $table->bigInteger('valor_novo_centavos')->nullable();
            $table->string('descricao_anterior', 255)->nullable();
            $table->string('descricao_nova', 255)->nullable();
            $table->foreignId('retificada_por')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('retificacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['retificada_por']);
            $table->dropColumn([
                'valor_anterior_centavos',
                'valor_novo_centavos',
                'descricao_anterior',
                'descricao_nova',
                'retificada_por',
            ]);
        });
    }
};

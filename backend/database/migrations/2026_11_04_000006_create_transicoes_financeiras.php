<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transicoes_financeiras', function (Blueprint $table) {
            $table->id();
            $table->date('inicio');
            $table->date('fim_previsto');
            $table->date('fim_real')->nullable();
            $table->string('status', 24)->default('ativa');
            $table->text('motivo');
            $table->bigInteger('saldo_inicial_centavos');
            $table->bigInteger('saldo_real_final_centavos')->nullable();
            $table->bigInteger('diferenca_conciliacao_centavos')->nullable();
            $table->text('justificativa_conciliacao')->nullable();
            $table->foreignId('responsavel_admin_id')->constrained('users');
            $table->foreignId('encerrada_por')->nullable()->constrained('users');
            $table->timestamp('relatorio_publicado_em')->nullable();
            $table->foreignId('relatorio_publicado_por')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('movimentacoes_transicao_financeira', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transicao_financeira_id')->constrained('transicoes_financeiras')->restrictOnDelete();
            $table->date('data');
            $table->string('tipo', 12);
            $table->string('categoria', 100);
            $table->string('descricao', 255);
            $table->text('justificativa_essencialidade');
            $table->bigInteger('valor_centavos');
            $table->string('status', 16)->default('pendente');
            $table->text('justificativa_cancelamento')->nullable();
            $table->foreignId('criada_por')->constrained('users');
            $table->foreignId('resolvida_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_transicao_financeira');
        Schema::dropIfExists('transicoes_financeiras');
    }
};

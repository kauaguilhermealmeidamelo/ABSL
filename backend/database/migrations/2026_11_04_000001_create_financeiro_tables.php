<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('gestoes_financeiras', function (Blueprint $t) {
            $t->id(); $t->string('nome'); $t->date('inicio'); $t->date('fim_previsto')->nullable();
            $t->unsignedBigInteger('saldo_inicial_centavos')->default(0);
            $t->string('status')->default('ativa');
            $t->date('encerrada_em')->nullable();
            $t->foreignId('criada_por')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('encerrada_por')->nullable()->constrained('users')->nullOnDelete();
            $t->text('observacao_encerramento')->nullable(); $t->timestamps();
        });
        Schema::create('movimentacoes_financeiras', function (Blueprint $t) {
            $t->id(); $t->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $t->date('data'); $t->string('tipo'); $t->string('categoria'); $t->string('descricao');
            $t->unsignedBigInteger('valor_centavos'); $t->string('status')->default('pendente');
            $t->foreignId('criada_por')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps(); $t->index(['gestao_financeira_id','status','data']);
        });
        Schema::create('comprovantes_financeiros', function (Blueprint $t) {
            $t->id(); $t->foreignId('movimentacao_financeira_id')->constrained('movimentacoes_financeiras')->restrictOnDelete();
            $t->string('arquivo_privado'); $t->string('arquivo_publico')->nullable(); $t->string('nome_original');
            $t->foreignId('enviado_por')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
        });
        Schema::create('relatorios_financeiros', function (Blueprint $t) {
            $t->id(); $t->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $t->unsignedSmallInteger('ano'); $t->unsignedTinyInteger('mes'); $t->string('status')->default('rascunho');
            $t->bigInteger('saldo_inicial_centavos')->default(0); $t->unsignedBigInteger('receitas_centavos')->default(0);
            $t->unsignedBigInteger('despesas_centavos')->default(0); $t->bigInteger('saldo_final_centavos')->default(0);
            $t->timestamp('publicado_em')->nullable(); $t->foreignId('publicado_por')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps(); $t->unique(['gestao_financeira_id','ano','mes']);
        });
        Schema::create('retificacoes_financeiras', function (Blueprint $t) {
            $t->id(); $t->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $t->foreignId('movimentacao_financeira_id')->nullable()->constrained('movimentacoes_financeiras')->restrictOnDelete();
            $t->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->text('justificativa'); $t->json('antes')->nullable(); $t->json('depois')->nullable(); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('retificacoes_financeiras'); Schema::dropIfExists('relatorios_financeiros');
        Schema::dropIfExists('comprovantes_financeiros'); Schema::dropIfExists('movimentacoes_financeiras');
        Schema::dropIfExists('gestoes_financeiras');
    }
};

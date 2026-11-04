<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vinculos_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('cargo', ['tesoureiro', 'tesoureiro_geral']);
            $table->boolean('interino')->default(false);
            $table->timestamp('inicio_em');
            $table->timestamp('fim_previsto_em')->nullable();
            $table->timestamp('fim_em')->nullable();
            $table->text('justificativa');
            $table->foreignId('nomeado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('encerrado_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('justificativa_encerramento')->nullable();
            $table->timestamps();

            $table->index(['gestao_financeira_id', 'cargo', 'fim_em'], 'vinc_fin_gestao_cargo_fim_idx');
            $table->index(['user_id', 'gestao_financeira_id', 'fim_em'], 'vinc_fin_usuario_gestao_fim_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vinculos_financeiros');
    }
};

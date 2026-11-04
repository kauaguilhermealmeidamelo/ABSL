<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('retificacoes_financeiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movimentacao_financeira_id')->constrained('movimentacoes_financeiras')->restrictOnDelete();
            $table->bigInteger('valor_anterior_centavos');
            $table->bigInteger('valor_novo_centavos');
            $table->string('descricao_anterior', 255);
            $table->string('descricao_nova', 255);
            $table->text('justificativa');
            $table->foreignId('retificada_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retificacoes_financeiras');
    }
};

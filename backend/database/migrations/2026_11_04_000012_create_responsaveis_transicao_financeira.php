<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('responsaveis_transicao_financeira', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transicao_financeira_id')->constrained(table: 'transicoes_financeiras', indexName: 'fk_responsavel_transicao')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('indicado_por')->constrained('users');
            $table->timestamp('indicado_em');
            $table->unique(['transicao_financeira_id', 'user_id'], 'uq_responsavel_transicao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsaveis_transicao_financeira');
    }
};

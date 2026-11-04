<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('esclarecimentos_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $table->foreignId('relatorio_financeiro_id')->nullable()->constrained('relatorios_financeiros')->restrictOnDelete();
            $table->text('texto');
            $table->foreignId('publicado_por')->constrained('users');
            $table->timestamp('publicado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esclarecimentos_financeiros');
    }
};

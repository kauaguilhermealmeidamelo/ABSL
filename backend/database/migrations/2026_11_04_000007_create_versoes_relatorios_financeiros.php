<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('versoes_relatorios_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_financeiro_id')->constrained(table: 'relatorios_financeiros', indexName: 'fk_versao_relatorio')->restrictOnDelete();
            $table->unsignedInteger('versao');
            $table->bigInteger('saldo_inicial_centavos');
            $table->bigInteger('receitas_centavos');
            $table->bigInteger('despesas_centavos');
            $table->bigInteger('saldo_final_centavos');
            $table->text('justificativa')->nullable();
            $table->longText('pdf_base64');
            $table->foreignId('publicado_por')->constrained('users')->restrictOnDelete();
            $table->timestamp('publicado_em');
            $table->timestamps();
            $table->unique(['relatorio_financeiro_id', 'versao'], 'uq_versao_relatorio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versoes_relatorios_financeiros');
    }
};

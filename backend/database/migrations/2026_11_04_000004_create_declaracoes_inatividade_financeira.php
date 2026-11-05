<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('declaracoes_inatividade_financeira', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestao_financeira_id')->unique()->constrained('gestoes_financeiras')->restrictOnDelete();
            $table->string('status', 24)->default('rascunho');
            $table->text('observacoes')->nullable();
            $table->unsignedInteger('versao')->default(1);
            $table->foreignId('criada_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('aprovada_tesouraria_por')->nullable()->constrained(table: 'users', indexName: 'fk_inatividade_tesouraria')->restrictOnDelete();
            $table->string('cargo_aprovador_tesouraria', 60)->nullable();
            $table->timestamp('aprovada_tesouraria_em')->nullable();
            $table->foreignId('aprovada_admin_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('aprovada_admin_em')->nullable();
            $table->timestamp('publicada_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaracoes_inatividade_financeira');
    }
};

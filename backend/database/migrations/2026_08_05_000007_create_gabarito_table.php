<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gabarito', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('disciplina'); // ex: Português, Matemática, etc
            $table->string('serie')->nullable(); // 1ª, 2ª, 3ª ano
            $table->string('tipo_prova'); // provão, simulado, etc
            $table->date('data_prova');
            $table->string('documento_url'); // URL do arquivo PDF/DOC
            $table->string('tipo_documento'); // pdf, doc, etc
            $table->timestamp('data_publicacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('publicado_por')->nullable();
            $table->timestamps();
            
            $table->foreign('publicado_por')->references('id')->on('users')->onDelete('set null');
            $table->index('disciplina');
            $table->index('serie');
            $table->index('data_prova');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gabarito');
    }
};
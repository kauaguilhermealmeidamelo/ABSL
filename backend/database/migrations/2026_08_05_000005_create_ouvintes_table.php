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
        Schema::create('ouvintes', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable(); // null se for anônimo
            $table->string('email')->nullable(); // null se for anônimo
            $table->string('turma')->nullable(); // 1A, 2B, etc
            $table->text('mensagem');
            $table->string('tipo')->default('sugestao'); // sugestao, reclamacao, elogio, etc
            $table->string('status')->default('pendente'); // pendente, respondido, fechado
            $table->text('resposta')->nullable();
            $table->timestamp('data_criacao')->useCurrent();
            $table->timestamp('data_resposta')->nullable();
            $table->timestamp('data_fechamento')->nullable();
            $table->unsignedBigInteger('respondido_por')->nullable();
            $table->boolean('anonimo')->default(false);
            $table->timestamps();
            
            $table->foreign('respondido_por')->references('id')->on('users')->onDelete('set null');
            $table->index('status');
            $table->index('data_criacao');
            $table->index('anonimo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouvintes');
    }
};
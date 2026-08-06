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
        Schema::create('horario', function (Blueprint $table) {
            $table->id();
            $table->string('turma'); // 1A, 1B, 2A, etc
            $table->string('dia_semana'); // Segunda, Terça, etc
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->string('disciplina');
            $table->string('professor')->nullable();
            $table->string('sala')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_criacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();
            
            $table->foreign('criado_por')->references('id')->on('users')->onDelete('set null');
            $table->index('turma');
            $table->index('dia_semana');
            $table->unique(['turma', 'dia_semana', 'horario_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
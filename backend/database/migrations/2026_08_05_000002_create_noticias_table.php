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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->longText('conteudo')->nullable();
            $table->string('imagem_url')->nullable();
            $table->timestamp('data_publicacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->timestamps();
            
            $table->foreign('autor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('data_publicacao');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
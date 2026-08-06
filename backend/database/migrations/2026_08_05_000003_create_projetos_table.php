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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->string('diretoria')->nullable(); // ex: "Diversidade", "Eventos", etc
            $table->string('categoria')->nullable();
            $table->string('imagem_url')->nullable();
            $table->text('conteudo_detalhado')->nullable();
            $table->string('status')->default('ativo'); // ativo, inativo, concluído
            $table->timestamp('data_criacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('destaque')->default(false);
            $table->unsignedBigInteger('responsavel_id')->nullable();
            $table->timestamps();
            
            $table->foreign('responsavel_id')->references('id')->on('users')->onDelete('set null');
            $table->index('diretoria');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
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
        Schema::create('transparencia', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->string('categoria'); // financeiro, administrativo, projetos, etc
            $table->string('arquivo_url')->nullable(); // URL do documento/PDF
            $table->string('tipo_documento'); // relatório, ata, orçamento, etc
            $table->date('data_documento');
            $table->timestamp('data_publicacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('publicado_por')->nullable();
            $table->timestamps();
            
            $table->foreign('publicado_por')->references('id')->on('users')->onDelete('set null');
            $table->index('categoria');
            $table->index('tipo_documento');
            $table->index('data_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transparencia');
    }
};
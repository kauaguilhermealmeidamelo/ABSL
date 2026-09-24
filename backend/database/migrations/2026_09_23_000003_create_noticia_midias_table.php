<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticia_midias', function (Blueprint $table) {
            $table->id();
            // cascadeOnDelete aqui é intencional e diferente do padrão
            // 'set null' usado em publicado_por/autor_id no resto do projeto:
            // uma mídia não existe sem a notícia dona dela, então ao excluir
            // a notícia as linhas de mídia devem sumir junto.
            $table->foreignId('noticia_id')->constrained('noticias')->cascadeOnDelete();
            $table->string('tipo'); // 'imagem' | 'video'
            $table->string('url');
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['noticia_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticia_midias');
    }
};
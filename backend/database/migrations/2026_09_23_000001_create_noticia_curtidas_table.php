<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticia_curtidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noticia_id')->constrained('noticias')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Impede curtida duplicada do mesmo usuário e torna o "toggle"
            // seguro contra duplo clique: se duas requisições chegarem quase
            // juntas, a segunda falha no INSERT em vez de criar linha
            // repetida ou zerar a contagem.
            $table->unique(['noticia_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticia_curtidas');
    }
};
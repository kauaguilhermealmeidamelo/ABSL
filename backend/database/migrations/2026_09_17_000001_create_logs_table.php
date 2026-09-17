<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('acao'); // ex: 'login', 'excluiu_usuario', 'criou_noticia'
            $table->string('entidade')->nullable(); // ex: 'usuario', 'noticia', 'projeto'
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nome')->nullable(); // snapshot: sobrevive à exclusão do usuário
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('acao');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
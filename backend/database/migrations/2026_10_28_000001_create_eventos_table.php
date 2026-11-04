<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->dateTime('data_evento');
            $table->string('imagem_url')->nullable();
            // Link externo de venda (Sympla, Eventbrite, Pix da tesouraria...).
            $table->string('link_ingresso', 500)->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->timestamps();

            $table->foreign('autor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('data_evento');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gremio_conteudos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('conteudo');
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();

            $table->foreign('criado_por')->references('id')->on('users')->onDelete('set null');
            $table->index('ordem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gremio_conteudos');
    }
};
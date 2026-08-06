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
        Schema::create('cardapio', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('dia_semana'); // Segunda, Terça, etc
            $table->text('refeicao'); // breakfast, lunch, dinner, etc
            $table->text('descricao'); // descrição do cardápio
            $table->text('observacoes')->nullable(); // alergênios, vegetariano, etc
            $table->boolean('ativo')->default(true);
            $table->timestamp('data_criacao')->useCurrent();
            $table->timestamp('data_atualizacao')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('criado_por')->nullable();
            $table->timestamps();
            
            $table->foreign('criado_por')->references('id')->on('users')->onDelete('set null');
            $table->index('data');
            $table->index('dia_semana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardapio');
    }
};
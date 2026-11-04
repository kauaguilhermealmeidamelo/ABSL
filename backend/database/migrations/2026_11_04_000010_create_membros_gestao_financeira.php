<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('membros_gestao_financeira', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestao_financeira_id')->constrained('gestoes_financeiras')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('cargo_institucional', 120);
            $table->text('justificativa');
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamp('desligado_em')->nullable();
            $table->timestamps();
            $table->unique(['gestao_financeira_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membros_gestao_financeira');
    }
};

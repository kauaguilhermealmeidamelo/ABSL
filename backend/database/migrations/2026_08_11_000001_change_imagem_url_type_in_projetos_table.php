<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sintaxe MODIFY é MySQL-only; no sqlite (testes) a coluna já é
        // string e comporta o uso — pula sem alterar nada.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('ALTER TABLE projetos MODIFY imagem_url LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('ALTER TABLE projetos MODIFY imagem_url VARCHAR(255) NULL');
    }
};

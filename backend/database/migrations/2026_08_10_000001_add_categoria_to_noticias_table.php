<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O frontend separa notícias em duas abas ("Notícias do Grêmio" /
     * "Notícias da Escola"), mas a tabela original não tinha coluna para
     * isso. Adicionamos aqui para o backend refletir o que a UI já espera.
     */
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('categoria')->default('gremio')->after('titulo');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropIndex(['categoria']);
            $table->dropColumn('categoria');
        });
    }
};

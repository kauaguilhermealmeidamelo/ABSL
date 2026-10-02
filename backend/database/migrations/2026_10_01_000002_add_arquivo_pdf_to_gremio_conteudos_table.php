<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gremio_conteudos', function (Blueprint $table) {
            $table->string('arquivo_pdf')->nullable()->after('conteudo');
        });
    }

    public function down(): void
    {
        Schema::table('gremio_conteudos', function (Blueprint $table) {
            $table->dropColumn('arquivo_pdf');
        });
    }
};

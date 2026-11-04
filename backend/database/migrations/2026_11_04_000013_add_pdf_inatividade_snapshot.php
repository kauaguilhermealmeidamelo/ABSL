<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('declaracoes_inatividade_financeira', function (Blueprint $table) {
            $table->longText('pdf_base64')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('declaracoes_inatividade_financeira', function (Blueprint $table) {
            $table->dropColumn('pdf_base64');
        });
    }
};

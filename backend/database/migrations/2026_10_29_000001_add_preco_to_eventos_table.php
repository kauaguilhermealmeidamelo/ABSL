<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->decimal('preco', 8, 2)->nullable()->after('link_ingresso');
            $table->boolean('gratuito')->default(false)->after('preco');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['preco', 'gratuito']);
        });
    }
};

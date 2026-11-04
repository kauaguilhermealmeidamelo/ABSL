<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('relatorios_financeiros', function (Blueprint $table) {
            $table->timestamp('fechado_em')->nullable();
            $table->foreignId('fechado_por')->nullable()->constrained('users');
            $table->unsignedInteger('versao')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('relatorios_financeiros', function (Blueprint $table) {
            $table->dropForeign(['fechado_por']);
            $table->dropColumn(['fechado_em', 'fechado_por', 'versao']);
        });
    }
};

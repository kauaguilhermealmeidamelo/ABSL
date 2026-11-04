<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('movimentacoes_financeiras', function (Blueprint $table) {
            $table->text('justificativa_cancelamento')->nullable();
            $table->timestamp('cancelada_em')->nullable();
            $table->foreignId('cancelada_por')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('movimentacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['cancelada_por']);
            $table->dropColumn(['justificativa_cancelamento', 'cancelada_em', 'cancelada_por']);
        });
    }
};

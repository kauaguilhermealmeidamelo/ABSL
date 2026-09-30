<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // @ do usuário (ex: @usuario). Nullable para não quebrar contas
            // antigas criadas antes deste campo; novos cadastros exigem valor.
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 30)->nullable()->unique()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
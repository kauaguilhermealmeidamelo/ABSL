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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar colunas se nao existirem
            if (!Schema::hasColumn('users', 'role')) {
                // SQLite usa CHECK fixo para ENUM; os cargos evoluem em migrations posteriores.
                // Em testes SQLite, string permite os mesmos cargos definidos no MySQL.
                if (Schema::getConnection()->getDriverName() === 'sqlite') {
                    $table->string('role')->default('user')->after('password');
                } else {
                    $table->enum('role', ['admin', 'user'])->default('user')->after('password');
                }
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'turma')) {
                $table->string('turma')->nullable()->after('is_admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_admin', 'turma']);
        });
    }
};

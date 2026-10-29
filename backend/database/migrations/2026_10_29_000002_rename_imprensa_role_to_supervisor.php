<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM é MySQL; no sqlite (testes) só migra os dados.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','imprensa','supervisor','user') NOT NULL DEFAULT 'user'");
        }

        DB::table('users')->where('role', 'imprensa')->update(['role' => 'supervisor']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','supervisor','user') NOT NULL DEFAULT 'user'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','imprensa','supervisor','user') NOT NULL DEFAULT 'user'");
        }

        DB::table('users')->where('role', 'supervisor')->update(['role' => 'imprensa']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','imprensa','user') NOT NULL DEFAULT 'user'");
        }
    }
};

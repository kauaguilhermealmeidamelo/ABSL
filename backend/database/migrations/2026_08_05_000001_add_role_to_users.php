<?php
// backend/database/migrations/2026_08_20_000001_add_imprensa_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL não permite alterar ENUM diretamente via Schema::table,
        // então usamos DB::statement.
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','imprensa','user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'imprensa'");
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
};
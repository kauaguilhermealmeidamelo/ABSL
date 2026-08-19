<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','imprensa','user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'user' WHERE role = 'imprensa'");
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
};

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = env('SEED_ADMIN_PASSWORD');

        if (! $adminPassword) {
            $adminPassword = Str::random(16);

            $this->command?->warn(
                "SEED_ADMIN_PASSWORD não definida — senha gerada para admin@absl.local: {$adminPassword}"
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@absl.local'],
            [
                'name' => 'Admin ABSL',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'is_admin' => true,
            ]
        );
    }
}
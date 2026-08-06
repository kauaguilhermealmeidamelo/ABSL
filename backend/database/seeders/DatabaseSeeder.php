<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário admin
        User::create([
            'name' => 'Admin ABSL',
            'email' => 'admin@absl.local',
            'password' => Hash::make('senha_super_segura_123'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Criar usuário comum
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@absl.local',
            'password' => Hash::make('senha_123'),
            'role' => 'user',
            'is_admin' => false,
            'turma' => '1A',
        ]);
    }
}
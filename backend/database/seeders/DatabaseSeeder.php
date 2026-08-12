<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Criar ou atualizar usuário admin
        User::updateOrCreate(
            ['email' => 'admin@absl.local'],
            [
                'name' => 'Admin ABSL',
                'password' => Hash::make('senha_super_segura_123'),
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        // Criar ou atualizar usuário comum
        User::updateOrCreate(
            ['email' => 'usuario@absl.local'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('senha_123'),
                'role' => 'user',
                'is_admin' => false,
                'turma' => '1A',
            ]
        );
    }
}
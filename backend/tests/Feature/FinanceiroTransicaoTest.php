<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroTransicaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_estudante_nao_pode_iniciar_transicao(): void
    {
        $estudante = User::factory()->create(['role' => 'user', 'is_admin' => false]);

        $this->actingAs($estudante)->postJson('/api/admin/financeiro/transicoes', [
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-01-31',
            'motivo' => 'Transição entre duas gestões.',
        ])->assertForbidden();
    }

    public function test_admin_pode_iniciar_transicao_com_saldo_zero_sem_gestao_anterior(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $this->actingAs($admin)->postJson('/api/admin/financeiro/transicoes', [
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-01-31',
            'motivo' => 'Transição administrativa extraordinária.',
        ])->assertCreated()->assertJsonPath('saldo_inicial_centavos', 0);

        $this->assertDatabaseHas('transicoes_financeiras', ['status' => 'ativa']);
        $this->assertDatabaseHas('responsaveis_transicao_financeira', ['user_id' => $admin->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroInatividadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_publica_inatividade_sem_duas_aprovacoes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão inativa',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 0,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);

        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade', [
            'observacoes' => 'Não houve movimentação financeira.',
        ])->assertOk();

        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade/publicar')
            ->assertUnprocessable();
    }
}

<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroPermissoesTest extends TestCase
{
    use RefreshDatabase;

    public function test_estudante_nao_pode_planejar_gestao(): void
    {
        $usuario = User::factory()->create(['role' => 'user', 'is_admin' => false]);

        $this->actingAs($usuario)->postJson('/api/admin/financeiro/gestoes', [
            'nome' => 'Gestão de teste',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
        ])->assertForbidden();
    }

    public function test_administrador_pode_planejar_sem_ativar(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);

        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes', [
            'nome' => 'Gestão de teste',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
        ])->assertCreated()->assertJsonPath('status', 'planejada');

        $this->assertDatabaseHas('gestoes_financeiras', [
            'nome' => 'Gestão de teste',
            'status' => 'planejada',
        ]);
    }

    public function test_estudante_nao_pode_criar_movimentacao_financeira(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $estudante = User::factory()->create(['role' => 'user', 'is_admin' => false]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 0,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);

        $this->actingAs($estudante)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/movimentacoes', [
            'data' => '2026-02-01',
            'tipo' => 'receita',
            'categoria' => 'Doação',
            'descricao' => 'Teste',
            'valor_centavos' => 1000,
        ])->assertForbidden();
    }
}

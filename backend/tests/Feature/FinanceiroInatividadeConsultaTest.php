<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroInatividadeConsultaTest extends TestCase
{
    use RefreshDatabase;

    private function gestao(User $admin): GestaoFinanceira
    {
        return GestaoFinanceira::create([
            'nome' => 'Gestão sem movimentações',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 0,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);
    }

    public function test_visitante_nao_consulta_rascunho(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = $this->gestao($admin);

        $this->getJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade')
            ->assertUnauthorized();
    }

    public function test_estudante_sem_vinculo_nao_consulta_rascunho(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $estudante = User::factory()->create(['role' => 'user', 'is_admin' => false]);
        $gestao = $this->gestao($admin);

        $this->actingAs($estudante)
            ->getJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade')
            ->assertForbidden();
    }

    public function test_admin_consulta_rascunho_sem_reiniciar_aprovacoes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = $this->gestao($admin);
        $url = '/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade';

        $this->actingAs($admin)->getJson($url)->assertOk()->assertSeeText('null');

        $this->actingAs($admin)->postJson($url, [
            'observacoes' => 'Não houve movimentação financeira neste período.',
        ])->assertOk()->assertJsonPath('versao', 1);

        $this->actingAs($admin)->getJson($url)
            ->assertOk()
            ->assertJsonPath('versao', 1)
            ->assertJsonPath('status', 'rascunho')
            ->assertJsonPath('observacoes', 'Não houve movimentação financeira neste período.');

        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'gestao_financeira_id' => $gestao->id,
            'versao' => 1,
        ]);
    }
}

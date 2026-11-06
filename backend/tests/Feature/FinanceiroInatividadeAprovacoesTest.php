<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinanceiroInatividadeAprovacoesTest extends TestCase
{
    use RefreshDatabase;

    private function preparar(): array
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $tesoureiro = User::factory()->create(['role' => 'user', 'is_admin' => false]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão inativa',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 0,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);
        $base = '/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade';
        $this->actingAs($admin)->postJson($base, [
            'observacoes' => 'Não houve movimentação financeira.',
        ])->assertOk();

        return [$admin, $tesoureiro, $gestao, $base];
    }

    private function vincular(GestaoFinanceira $gestao, User $admin, User $tesoureiro): void
    {
        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/vinculos', [
            'user_id' => $tesoureiro->id,
            'cargo' => 'tesoureiro_geral',
            'justificativa' => 'Nomeação para exercer a Tesouraria-Geral.',
        ])->assertCreated();
    }

    public function test_usuario_comum_nao_aprova_como_administracao(): void
    {
        [, $usuario, , $base] = $this->preparar();

        $this->actingAs($usuario)->postJson($base.'/aprovar', [
            'papel' => 'administracao',
        ])->assertForbidden();

        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'aprovada_admin_por' => null,
        ]);
    }

    public function test_administrador_sem_vinculo_nao_aprova_como_tesouraria(): void
    {
        [$admin, , , $base] = $this->preparar();

        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'tesouraria',
        ])->assertForbidden();
    }

    public function test_duas_pessoas_distintas_aprovam_e_rascunho_reinicia_aprovacoes(): void
    {
        [$admin, $tesoureiro, $gestao, $base] = $this->preparar();
        $this->vincular($gestao, $admin, $tesoureiro);

        $this->actingAs($tesoureiro)->postJson($base.'/aprovar', [
            'papel' => 'tesouraria',
        ])->assertOk()->assertJsonPath('aprovada_tesouraria_por', $tesoureiro->id);

        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'administracao',
        ])->assertOk()->assertJsonPath('aprovada_admin_por', $admin->id);

        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'gestao_financeira_id' => $gestao->id,
            'aprovada_tesouraria_por' => $tesoureiro->id,
            'aprovada_admin_por' => $admin->id,
        ]);

        $this->actingAs($admin)->postJson($base, [
            'observacoes' => 'Atualização das observações da declaração.',
        ])->assertOk()->assertJsonPath('versao', 2)
            ->assertJsonPath('aprovada_tesouraria_por', null)
            ->assertJsonPath('aprovada_admin_por', null);
    }

    public function test_mesmo_usuario_nao_pode_aprovar_ambos_os_papeis(): void
    {
        [$admin, , $gestao, $base] = $this->preparar();
        $this->vincular($gestao, $admin, $admin);

        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'tesouraria',
        ])->assertOk();

        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'administracao',
        ])->assertUnprocessable();

        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'gestao_financeira_id' => $gestao->id,
            'aprovada_tesouraria_por' => $admin->id,
            'aprovada_admin_por' => null,
        ]);
    }

    public function test_publicacao_bloqueada_sem_conciliacao_mesmo_com_duas_aprovacoes(): void
    {
        [$admin, $tesoureiro, $gestao, $base] = $this->preparar();
        $this->vincular($gestao, $admin, $tesoureiro);

        $this->actingAs($tesoureiro)->postJson($base.'/aprovar', [
            'papel' => 'tesouraria',
        ])->assertOk();
        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'administracao',
        ])->assertOk();

        $this->actingAs($admin)->postJson($base.'/publicar')->assertUnprocessable();

        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'gestao_financeira_id' => $gestao->id,
            'status' => 'rascunho',
        ]);
    }
}

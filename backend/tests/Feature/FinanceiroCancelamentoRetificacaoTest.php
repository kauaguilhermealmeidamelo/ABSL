<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroCancelamentoRetificacaoTest extends TestCase
{
    use RefreshDatabase;

    private function preparar(): array
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão de testes financeiros',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 10000,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);
        $movimentacao = MovimentacaoFinanceira::create([
            'gestao_financeira_id' => $gestao->id,
            'data' => '2026-01-10',
            'tipo' => 'despesa',
            'categoria' => 'materiais',
            'descricao' => 'Compra de materiais',
            'valor_centavos' => 2500,
            'status' => 'pendente',
            'criada_por' => $admin->id,
        ]);
        return [$admin, $gestao, $movimentacao];
    }

    public function test_cancelamento_exige_justificativa_e_preserva_saldo(): void
    {
        [$admin, $gestao, $movimentacao] = $this->preparar();
        $url = '/api/admin/financeiro/movimentacoes/'.$movimentacao->id.'/cancelar';

        $this->actingAs($admin)->postJson($url, [
            'justificativa' => 'curta',
        ])->assertUnprocessable();

        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'id' => $movimentacao->id, 'status' => 'pendente',
        ]);

        $this->actingAs($admin)->postJson($url, [
            'justificativa' => 'Compra cancelada antes da confirmação.',
        ])->assertOk()->assertJsonPath('status', 'cancelada');

        $this->assertSame(10000, app(\App\Services\FinanceiroService::class)
            ->totais($gestao)['saldo_final_centavos']);

        $this->actingAs($admin)->postJson($url, [
            'justificativa' => 'Tentativa de cancelar novamente.',
        ])->assertUnprocessable();
    }

    public function test_estudante_nao_cancela_movimentacao(): void
    {
        [, , $movimentacao] = $this->preparar();
        $estudante = User::factory()->create(['role' => 'user', 'is_admin' => false]);

        $this->actingAs($estudante)
            ->postJson('/api/admin/financeiro/movimentacoes/'.$movimentacao->id.'/cancelar', [
                'justificativa' => 'Tentativa não autorizada de cancelamento.',
            ])->assertForbidden();

        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'id' => $movimentacao->id, 'status' => 'pendente',
        ]);
    }

    public function test_retificacao_nao_altera_movimentacao_pendente(): void
    {
        [$admin, , $movimentacao] = $this->preparar();

        $this->actingAs($admin)
            ->postJson('/api/admin/financeiro/movimentacoes/'.$movimentacao->id.'/retificar', [
                'valor_centavos' => 3500,
                'descricao' => 'Materiais retificados',
                'justificativa' => 'Ajuste solicitado pela Tesouraria.',
            ])->assertUnprocessable();

        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'id' => $movimentacao->id,
            'valor_centavos' => 2500,
            'status' => 'pendente',
        ]);
    }

    public function test_retificacao_exige_mes_publicado_e_preserva_movimentacao_original(): void
    {
        [$admin, , $movimentacao] = $this->preparar();
        $movimentacao->update(['status' => 'confirmada']);

        $this->actingAs($admin)
            ->postJson('/api/admin/financeiro/movimentacoes/'.$movimentacao->id.'/retificar', [
                'valor_centavos' => 3500,
                'descricao' => 'Materiais retificados',
                'justificativa' => 'Correção após conferência documental.',
            ])->assertUnprocessable();

        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'id' => $movimentacao->id,
            'valor_centavos' => 2500,
            'descricao' => 'Compra de materiais',
            'status' => 'confirmada',
        ]);
        $this->assertDatabaseMissing('retificacoes_financeiras', [
            'movimentacao_financeira_id' => $movimentacao->id,
        ]);
    }
}

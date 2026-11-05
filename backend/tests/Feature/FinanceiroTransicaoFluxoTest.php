<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinanceiroTransicaoFluxoTest extends TestCase
{
    use RefreshDatabase;

    private function iniciar(User $admin): int
    {
        $resposta = $this->actingAs($admin)->postJson('/api/admin/financeiro/transicoes', [
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-01-31',
            'motivo' => 'Período de transição financeira entre gestões.',
        ])->assertCreated();

        return (int) $resposta->json('id');
    }

    private function movimentar(User $admin, int $transicao, string $tipo, int $valor, string $data = '2026-01-10'): int
    {
        $resposta = $this->actingAs($admin)->postJson("/api/admin/financeiro/transicoes/{$transicao}/movimentacoes", [
            'data' => $data,
            'tipo' => $tipo,
            'categoria' => 'Operações essenciais',
            'descricao' => 'Operação financeira durante a transição',
            'justificativa_essencialidade' => 'Despesa ou receita essencial para a continuidade das atividades.',
            'valor_centavos' => $valor,
        ])->assertCreated()->assertJsonPath('status', 'pendente');

        return (int) $resposta->json('id');
    }

    public function test_nao_abre_duas_transicoes_ativas(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $this->iniciar($admin);

        $this->actingAs($admin)->postJson('/api/admin/financeiro/transicoes', [
            'inicio' => '2026-02-01',
            'fim_previsto' => '2026-02-28',
            'motivo' => 'Outra transição administrativa extraordinária.',
        ])->assertUnprocessable();

        $this->assertSame(1, DB::table('transicoes_financeiras')->count());
    }

    public function test_movimentacao_fora_do_periodo_e_valor_invalido_sao_rejeitados(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $transicao = $this->iniciar($admin);
        $url = "/api/admin/financeiro/transicoes/{$transicao}/movimentacoes";
        $dados = [
            'data' => '2026-02-01',
            'tipo' => 'receita',
            'categoria' => 'Doações',
            'descricao' => 'Doação recebida fora do período',
            'justificativa_essencialidade' => 'Recebimento necessário para continuidade financeira.',
            'valor_centavos' => 1000,
        ];

        $this->actingAs($admin)->postJson($url, $dados)->assertUnprocessable();
        $this->actingAs($admin)->postJson($url, [
            ...$dados, 'data' => '2026-01-10', 'valor_centavos' => 0,
        ])->assertUnprocessable();

        $this->assertSame(0, DB::table('movimentacoes_transicao_financeira')->count());
    }

    public function test_pendencias_impedem_encerramento_e_cancelamento_nao_altera_saldo(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $transicao = $this->iniciar($admin);
        $movimentacao = $this->movimentar($admin, $transicao, 'despesa', 3000);
        $encerrar = "/api/admin/financeiro/transicoes/{$transicao}/encerrar";

        $this->actingAs($admin)->postJson($encerrar, [
            'fim_real' => '2026-01-31',
            'saldo_real_final_centavos' => 0,
        ])->assertUnprocessable();

        $this->actingAs($admin)->postJson(
            "/api/admin/financeiro/transicoes/{$transicao}/movimentacoes/{$movimentacao}/resolver",
            ['acao' => 'cancelar', 'justificativa' => 'Lançamento duplicado identificado na conferência.']
        )->assertNoContent();

        $this->actingAs($admin)->postJson($encerrar, [
            'fim_real' => '2026-01-31',
            'saldo_real_final_centavos' => 0,
        ])->assertOk()->assertJsonPath('saldo_contabil_centavos', 0);

        $this->assertDatabaseHas('transicoes_financeiras', [
            'id' => $transicao,
            'status' => 'encerrada',
            'saldo_real_final_centavos' => 0,
        ]);
        $this->assertDatabaseHas('movimentacoes_transicao_financeira', [
            'id' => $movimentacao, 'status' => 'cancelada',
        ]);
    }

    public function test_receita_e_despesa_confirmadas_compõem_saldo_e_pdf_publicado(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $transicao = $this->iniciar($admin);
        $receita = $this->movimentar($admin, $transicao, 'receita', 5000);
        $despesa = $this->movimentar($admin, $transicao, 'despesa', 2000);

        foreach ([$receita, $despesa] as $movimentacao) {
            $this->actingAs($admin)->postJson(
                "/api/admin/financeiro/transicoes/{$transicao}/movimentacoes/{$movimentacao}/resolver",
                ['acao' => 'confirmar']
            )->assertNoContent();
        }

        $this->actingAs($admin)->postJson("/api/admin/financeiro/transicoes/{$transicao}/encerrar", [
            'fim_real' => '2026-01-31',
            'saldo_real_final_centavos' => 3000,
        ])->assertOk()->assertJsonPath('saldo_contabil_centavos', 3000);

        $this->assertDatabaseHas('transicoes_financeiras', [
            'id' => $transicao, 'status' => 'encerrada',
            'saldo_real_final_centavos' => 3000,
            'diferenca_conciliacao_centavos' => 0,
        ]);
        $this->assertNotEmpty(DB::table('transicoes_financeiras')->where('id', $transicao)->value('pdf_base64'));
        $this->getJson("/api/financeiro/transicoes/{$transicao}/relatorio")
            ->assertOk()->assertJsonPath('transicao.saldo_real_final_centavos', 3000);
    }

    public function test_diferenca_de_conciliacao_exige_justificativa(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $transicao = $this->iniciar($admin);
        $url = "/api/admin/financeiro/transicoes/{$transicao}/encerrar";

        $this->actingAs($admin)->postJson($url, [
            'fim_real' => '2026-01-31',
            'saldo_real_final_centavos' => 100,
        ])->assertUnprocessable();

        $this->actingAs($admin)->postJson($url, [
            'fim_real' => '2026-01-31',
            'saldo_real_final_centavos' => 100,
            'justificativa_conciliacao' => 'Diferença identificada na conferência do caixa.',
        ])->assertOk();

        $this->assertDatabaseHas('transicoes_financeiras', [
            'id' => $transicao, 'diferenca_conciliacao_centavos' => 100,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_nao_pode_criar_gestao(): void
    {
        $this->postJson('/api/admin/financeiro/gestoes', [
            'nome' => 'Gestão de teste', 'inicio' => '2026-01-01', 'saldo_inicial_centavos' => 10000,
        ])->assertUnauthorized();
    }

    public function test_supervisor_nao_pode_encerrar_gestao(): void
    {
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão de teste', 'inicio' => '2026-01-01',
            'saldo_inicial_centavos' => 10000, 'status' => 'ativa',
        ]);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'is_admin' => false]);
        $this->actingAs($supervisor)->postJson("/api/admin/financeiro/gestoes/{$gestao->id}/encerrar", [
            'observacao' => 'Fechamento de teste autorizado',
        ])->assertForbidden();
    }

    public function test_resumo_considera_apenas_confirmadas(): void
    {
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão de teste', 'inicio' => '2026-01-01',
            'saldo_inicial_centavos' => 10000, 'status' => 'ativa',
        ]);
        foreach ([
            ['receita', 5000, 'confirmada'],
            ['despesa', 2000, 'confirmada'],
            ['despesa', 9000, 'pendente'],
        ] as [$tipo, $valor, $status]) {
            MovimentacaoFinanceira::create([
                'gestao_financeira_id' => $gestao->id, 'data' => '2026-01-10',
                'tipo' => $tipo, 'valor_centavos' => $valor, 'status' => $status,
                'categoria' => 'teste', 'descricao' => 'Teste automatizado',
            ]);
        }
        $totais = app(\App\Services\FinanceiroService::class)->totais($gestao);
        $this->assertSame(13000, $totais['saldo_final_centavos']);
        $this->assertSame(5000, $totais['receitas_centavos']);
        $this->assertSame(2000, $totais['despesas_centavos']);
    }
    public function test_relatorio_publicado_bloqueia_nova_movimentacao_no_mes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão de teste', 'inicio' => '2026-01-01',
            'saldo_inicial_centavos' => 10000, 'status' => 'ativa',
        ]);
        $this->actingAs($admin)->postJson("/api/admin/financeiro/gestoes/{$gestao->id}/relatorios/fechar", [
            'ano' => 2026, 'mes' => 1,
        ])->assertCreated();
        $this->actingAs($admin)->postJson("/api/admin/financeiro/gestoes/{$gestao->id}/relatorios", [
            'ano' => 2026, 'mes' => 1,
        ])->assertOk();
        $this->actingAs($admin)->postJson("/api/admin/financeiro/gestoes/{$gestao->id}/movimentacoes", [
            'data' => '2026-01-10', 'tipo' => 'receita',
            'categoria' => 'doacoes', 'descricao' => 'Doação de teste', 'valor_centavos' => 1000,
        ])->assertUnprocessable();
    }

    public function test_publico_consulta_apenas_relatorios_publicados(): void
    {
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão de teste', 'inicio' => '2026-01-01',
            'saldo_inicial_centavos' => 10000, 'status' => 'ativa',
        ]);
        \App\Models\RelatorioFinanceiro::create([
            'gestao_financeira_id' => $gestao->id, 'ano' => 2026, 'mes' => 1,
            'status' => 'publicado', 'saldo_inicial_centavos' => 10000,
            'receitas_centavos' => 0, 'despesas_centavos' => 0,
            'saldo_final_centavos' => 10000, 'publicado_em' => now(),
        ]);
        \App\Models\RelatorioFinanceiro::create([
            'gestao_financeira_id' => $gestao->id, 'ano' => 2026, 'mes' => 2,
            'status' => 'rascunho', 'saldo_inicial_centavos' => 10000,
            'receitas_centavos' => 0, 'despesas_centavos' => 0,
            'saldo_final_centavos' => 10000,
        ]);
        $this->getJson('/api/financeiro/relatorios')->assertOk()->assertJsonCount(1);
    }

}

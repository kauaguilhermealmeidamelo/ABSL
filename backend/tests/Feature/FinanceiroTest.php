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
}

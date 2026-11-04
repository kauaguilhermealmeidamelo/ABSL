<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use App\Services\FinanceiroService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroCalculosTest extends TestCase
{
    use RefreshDatabase;

    public function test_somente_movimentacoes_confirmadas_afetam_saldo_em_centavos(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 10005,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);
        foreach ([
            ['receita', 'confirmada', 2550],
            ['despesa', 'confirmada', 1234],
            ['despesa', 'pendente', 9000],
            ['receita', 'cancelada', 5000],
        ] as [$tipo, $status, $valor]) {
            $gestao->movimentacoes()->create([
                'data' => '2026-01-15',
                'tipo' => $tipo,
                'categoria' => 'Teste',
                'descricao' => 'Movimentação de teste',
                'valor_centavos' => $valor,
                'status' => $status,
                'criada_por' => $admin->id,
            ]);
        }

        $totais = app(FinanceiroService::class)->totais($gestao);
        $this->assertSame(2550, $totais['receitas_centavos']);
        $this->assertSame(1234, $totais['despesas_centavos']);
        $this->assertSame(11321, $totais['saldo_final_centavos']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceiroFechamentoMensalTest extends TestCase
{
    use RefreshDatabase;

    public function test_fecha_mes_sem_movimentacoes_e_publica_separadamente(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = GestaoFinanceira::create([
            'nome' => 'Gestão 2026',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-12-31',
            'saldo_inicial_centavos' => 10000,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);

        $url = '/api/admin/financeiro/gestoes/'.$gestao->id.'/relatorios';
        $this->actingAs($admin)->postJson($url, ['ano' => 2026, 'mes' => 1])
            ->assertUnprocessable();

        $this->actingAs($admin)->postJson($url.'/fechar', ['ano' => 2026, 'mes' => 1])
            ->assertCreated()->assertJsonPath('status', 'fechado')
            ->assertJsonPath('saldo_final_centavos', 10000);

        $this->actingAs($admin)->postJson($url, ['ano' => 2026, 'mes' => 1])
            ->assertOk()->assertJsonPath('status', 'publicado');

        $this->assertDatabaseHas('versoes_relatorios_financeiros', [
            'versao' => 1,
            'saldo_final_centavos' => 10000,
        ]);
    }
}

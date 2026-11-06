<?php

namespace Tests\Feature;

use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinanceiroFluxosPositivosTest extends TestCase
{
    use RefreshDatabase;

    private function gestao(User $admin, int $saldo = 0): GestaoFinanceira
    {
        return GestaoFinanceira::create([
            'nome' => 'Gestão janeiro 2026',
            'inicio' => '2026-01-01',
            'fim_previsto' => '2026-01-31',
            'saldo_inicial_centavos' => $saldo,
            'status' => 'ativa',
            'criada_por' => $admin->id,
        ]);
    }

    private function publicarJaneiro(User $admin, GestaoFinanceira $gestao): void
    {
        $base = '/api/admin/financeiro/gestoes/'.$gestao->id.'/relatorios';
        $this->actingAs($admin)->postJson($base.'/fechar', [
            'ano' => 2026, 'mes' => 1,
        ])->assertCreated();
        $this->actingAs($admin)->postJson($base, [
            'ano' => 2026, 'mes' => 1,
        ])->assertOk()->assertJsonPath('status', 'publicado');
    }

    public function test_retificacao_publicada_preserva_pdf_original_e_cria_nova_versao(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $gestao = $this->gestao($admin, 10000);
        $movimentacao = MovimentacaoFinanceira::create([
            'gestao_financeira_id' => $gestao->id,
            'data' => '2026-01-10',
            'tipo' => 'despesa',
            'categoria' => 'materiais',
            'descricao' => 'Compra original',
            'valor_centavos' => 2000,
            'status' => 'confirmada',
            'criada_por' => $admin->id,
        ]);
        $this->publicarJaneiro($admin, $gestao);

        $relatorio = DB::table('relatorios_financeiros')
            ->where('gestao_financeira_id', $gestao->id)->first();
        $original = DB::table('versoes_relatorios_financeiros')
            ->where('relatorio_financeiro_id', $relatorio->id)->where('versao', 1)->first();
        $this->assertNotNull($original);
        $this->assertNotEmpty($original->pdf_base64);

        $this->actingAs($admin)->postJson(
            '/api/admin/financeiro/movimentacoes/'.$movimentacao->id.'/retificar',
            [
                'valor_centavos' => 3000,
                'descricao' => 'Compra corrigida',
                'justificativa' => 'Correção documental do valor registrado.',
            ]
        )->assertOk()->assertJsonPath('relatorios_atualizados', 1);

        $this->assertDatabaseHas('relatorios_financeiros', [
            'id' => $relatorio->id, 'versao' => 2, 'saldo_final_centavos' => 7000,
        ]);
        $this->assertDatabaseHas('versoes_relatorios_financeiros', [
            'relatorio_financeiro_id' => $relatorio->id,
            'versao' => 1, 'saldo_final_centavos' => 8000,
        ]);
        $this->assertDatabaseHas('versoes_relatorios_financeiros', [
            'relatorio_financeiro_id' => $relatorio->id,
            'versao' => 2, 'saldo_final_centavos' => 7000,
        ]);
        $this->assertSame($original->pdf_base64, DB::table('versoes_relatorios_financeiros')
            ->where('relatorio_financeiro_id', $relatorio->id)
            ->where('versao', 1)->value('pdf_base64'));
        $this->assertDatabaseHas('retificacoes_financeiras', [
            'movimentacao_financeira_id' => $movimentacao->id,
            'valor_anterior_centavos' => 2000,
            'valor_novo_centavos' => 3000,
        ]);
    }

    public function test_publicacao_definitiva_de_inatividade_exige_e_preserva_duas_aprovacoes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $tesoureiro = User::factory()->create(['role' => 'user', 'is_admin' => false]);
        $gestao = $this->gestao($admin);
        $base = '/api/admin/financeiro/gestoes/'.$gestao->id.'/inatividade';

        $this->publicarJaneiro($admin, $gestao);
        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/conciliacao', [
            'saldo_real_final_centavos' => 0,
        ])->assertOk();
        $this->actingAs($admin)->postJson('/api/admin/financeiro/gestoes/'.$gestao->id.'/vinculos', [
            'user_id' => $tesoureiro->id,
            'cargo' => 'tesoureiro_geral',
            'justificativa' => 'Responsável pela validação financeira.',
        ])->assertCreated();

        $this->actingAs($admin)->postJson($base, [
            'observacoes' => 'Nenhuma movimentação registrada no período.',
        ])->assertOk();
        $this->actingAs($tesoureiro)->postJson($base.'/aprovar', [
            'papel' => 'tesouraria',
        ])->assertOk();
        $this->actingAs($admin)->postJson($base.'/aprovar', [
            'papel' => 'administracao',
        ])->assertOk();

        $this->actingAs($admin)->postJson($base.'/publicar')
            ->assertOk()->assertJsonPath('status', 'publicada');
        $this->assertDatabaseHas('declaracoes_inatividade_financeira', [
            'gestao_financeira_id' => $gestao->id,
            'status' => 'publicada',
            'aprovada_tesouraria_por' => $tesoureiro->id,
            'aprovada_admin_por' => $admin->id,
        ]);
        $this->assertNotEmpty(DB::table('declaracoes_inatividade_financeira')
            ->where('gestao_financeira_id', $gestao->id)->value('pdf_base64'));
        $this->getJson('/api/financeiro/gestoes/'.$gestao->id.'/inatividade')
            ->assertOk()->assertJsonPath('declaracao.status', 'publicada');
        $this->actingAs($admin)->postJson($base, [
            'observacoes' => 'Tentativa de alteração após publicação.',
        ])->assertUnprocessable();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Services\AutorizacaoFinanceira;
use App\Services\VersaoRelatorioFinanceiroService;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RetificacaoFinanceiraController extends Controller
{
    public function retificar(Request $request, MovimentacaoFinanceira $movimentacao, AutorizacaoFinanceira $autorizacao, VersaoRelatorioFinanceiroService $versoes)
    {
        $dados = $request->validate([
            'valor_centavos' => ['required', 'integer', 'min:1'],
            'descricao' => ['required', 'string', 'max:255'],
            'justificativa' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $resultado = DB::transaction(function () use ($request, $movimentacao, $autorizacao, $versoes, $dados) {
            $gestao = GestaoFinanceira::whereKey($movimentacao->gestao_financeira_id)->lockForUpdate()->firstOrFail();
            abort_unless($autorizacao->podeGerenciarFechamento($request->user(), $gestao), 403);
            if (DB::table('declaracoes_inatividade_financeira')->where('gestao_financeira_id', $gestao->id)
                ->where('status', 'publicada')->exists()) {
                throw ValidationException::withMessages(['gestao' => 'Declaração de inatividade publicada: retificações bloqueadas.']);
            }
            $registro = MovimentacaoFinanceira::whereKey($movimentacao->id)->lockForUpdate()->firstOrFail();

            if ($registro->status !== 'confirmada') {
                throw ValidationException::withMessages(['movimentacao' => 'Somente movimentações confirmadas podem ser retificadas.']);
            }
            if ($registro->valor_centavos === $dados['valor_centavos'] && $registro->descricao === $dados['descricao']) {
                throw ValidationException::withMessages(['movimentacao' => 'Nenhuma alteração foi informada.']);
            }
            $mesOrigem = sprintf('%04d-%02d', $registro->data->year, $registro->data->month);
            $relatorios = RelatorioFinanceiro::where('gestao_financeira_id', $gestao->id)
                ->whereRaw('(ano * 100 + mes) >= ?', [$registro->data->year * 100 + $registro->data->month])
                ->orderBy('ano')->orderBy('mes')->lockForUpdate()->get();

            if ($relatorios->isEmpty() || $relatorios->first()->ano !== $registro->data->year
                || $relatorios->first()->mes !== $registro->data->month) {
                throw ValidationException::withMessages(['movimentacao' => 'O mês original ainda não foi fechado.']);
            }
            foreach ($relatorios as $relatorio) {
                if ($relatorio->status !== 'publicado') {
                    throw ValidationException::withMessages(['relatorio' => 'Publique todos os meses afetados antes de retificar.']);
                }
            }

            foreach ($relatorios as $relatorio) {
                if (! DB::table('versoes_relatorios_financeiros')
                    ->where('relatorio_financeiro_id', $relatorio->id)
                    ->where('versao', $relatorio->versao)->exists()) {
                    throw ValidationException::withMessages(['relatorio' => 'A versão original deve ter PDF preservado antes da retificação.']);
                }
            }

            DB::table('retificacoes_financeiras')->insert([
                'movimentacao_financeira_id' => $registro->id,
                'valor_anterior_centavos' => $registro->valor_centavos,
                'valor_novo_centavos' => $dados['valor_centavos'],
                'descricao_anterior' => $registro->descricao,
                'descricao_nova' => $dados['descricao'],
                'justificativa' => $dados['justificativa'],
                'retificada_por' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $registro->update([
                'valor_centavos' => $dados['valor_centavos'],
                'descricao' => $dados['descricao'],
            ]);

            foreach ($relatorios as $relatorio) {
                $inicio = sprintf('%04d-%02d-01', $relatorio->ano, $relatorio->mes);
                $fim = date('Y-m-d', strtotime($inicio.' +1 month'));
                $somas = fn ($query) => $query->where('status', 'confirmada')
                    ->selectRaw('tipo, SUM(valor_centavos) as total')
                    ->groupBy('tipo')->pluck('total', 'tipo');
                $anteriores = $somas($gestao->movimentacoes()->where('data', '<', $inicio));
                $atuais = $somas($gestao->movimentacoes()->where('data', '>=', $inicio)->where('data', '<', $fim));
                $saldo = (int) $gestao->saldo_inicial_centavos
                    + (int) ($anteriores['receita'] ?? 0) - (int) ($anteriores['despesa'] ?? 0);
                $receitas = (int) ($atuais['receita'] ?? 0);
                $despesas = (int) ($atuais['despesa'] ?? 0);
                $relatorio->update([
                    'versao' => $relatorio->versao + 1,
                    'saldo_inicial_centavos' => $saldo,
                    'receitas_centavos' => $receitas,
                    'despesas_centavos' => $despesas,
                    'saldo_final_centavos' => $saldo + $receitas - $despesas,
                    'publicado_em' => now(),
                    'publicado_por' => $request->user()->id,
                ]);
                $versoes->registrar($relatorio, $request->user()->id, $dados['justificativa'].' (origem '.$mesOrigem.')');
            }

            // O saldo real previamente conciliado permanece intacto; a diferença deve ser reconciliada.
            Auditoria::registrar('retificou_movimentacao_financeira', descricao: $dados['justificativa'], entidade: 'movimentacao_financeira', entidadeId: $registro->id);
            return ['movimentacao' => $registro->fresh(), 'relatorios_atualizados' => $relatorios->count()];
        });
        return response()->json($resultado);
    }
}

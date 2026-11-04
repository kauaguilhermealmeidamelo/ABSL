<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FechamentoTransicaoController extends Controller
{
    public function encerrar(Request $request, int $transicao)
    {
        $dados = $request->validate([
            'saldo_real_final_centavos' => ['required', 'integer', 'min:0'],
            'justificativa_conciliacao' => ['nullable', 'string', 'max:3000'],
            'fim_real' => ['required', 'date'],
        ]);
        $resultado = DB::transaction(function () use ($request, $transicao, $dados) {
            $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)->lockForUpdate()->first();
            abort_unless($periodo, 404);
            if ($periodo->status !== 'ativa' || $dados['fim_real'] < $periodo->inicio
                || $dados['fim_real'] > $periodo->fim_previsto || $dados['fim_real'] > today()->toDateString()) {
                throw ValidationException::withMessages(['transicao' => 'Transição ou data inválida.']);
            }
            $movimentos = DB::table('movimentacoes_transicao_financeira')->where('transicao_financeira_id', $transicao);
            if ((clone $movimentos)->where('status', 'pendente')->exists()) {
                throw ValidationException::withMessages(['transicao' => 'Resolva todas as pendências.']);
            }
            $receitas = (int) (clone $movimentos)->where('status', 'confirmada')->where('tipo', 'receita')->sum('valor_centavos');
            $despesas = (int) (clone $movimentos)->where('status', 'confirmada')->where('tipo', 'despesa')->sum('valor_centavos');
            $contabil = $periodo->saldo_inicial_centavos + $receitas - $despesas;
            $diferenca = $dados['saldo_real_final_centavos'] - $contabil;
            if ($diferenca !== 0 && mb_strlen(trim($dados['justificativa_conciliacao'] ?? '')) < 10) {
                throw ValidationException::withMessages(['justificativa_conciliacao' => 'Justifique a diferença de saldo.']);
            }
            DB::table('transicoes_financeiras')->where('id', $transicao)->update([
                'status' => 'encerrada', 'fim_real' => $dados['fim_real'],
                'saldo_real_final_centavos' => $dados['saldo_real_final_centavos'],
                'diferenca_conciliacao_centavos' => $diferenca,
                'justificativa_conciliacao' => $dados['justificativa_conciliacao'] ?? null,
                'encerrada_por' => $request->user()->id,
                'relatorio_publicado_em' => now(), 'relatorio_publicado_por' => $request->user()->id,
                'updated_at' => now(),
            ]);
            Auditoria::registrar('encerrou_transicao_financeira', entidade: 'transicao_financeira', entidadeId: $transicao);
            return ['saldo_contabil_centavos' => $contabil, 'saldo_real_final_centavos' => $dados['saldo_real_final_centavos']];
        });
        return response()->json($resultado);
    }

    public function relatorio(int $transicao)
    {
        $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)
            ->whereNotNull('relatorio_publicado_em')->first();
        abort_unless($periodo, 404);
        $movimentos = DB::table('movimentacoes_transicao_financeira')
            ->where('transicao_financeira_id', $transicao)->get();
        return response()->json(['transicao' => $periodo, 'movimentacoes' => $movimentos]);
    }
}

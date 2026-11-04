<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Services\AutorizacaoFinanceira;
use App\Services\FinanceiroService;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConciliacaoFinanceiraController extends Controller
{
    public function registrar(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao, FinanceiroService $financeiro)
    {
        $dados = $request->validate([
            'saldo_real_final_centavos' => ['required', 'integer', 'min:0'],
            'justificativa' => ['nullable', 'string', 'max:2000'],
        ]);

        $resultado = DB::transaction(function () use ($request, $gestao, $autorizacao, $financeiro, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            abort_unless($autorizacao->podeGerenciarFechamento($request->user(), $gestao), 403);

            if ($gestao->movimentacoes()->where('status', 'pendente')->exists()) {
                throw ValidationException::withMessages(['gestao' => 'Resolva as movimentações pendentes antes da conciliação.']);
            }

            $saldoContabil = $financeiro->totais($gestao)['saldo_final_centavos'];
            $diferenca = (int) $dados['saldo_real_final_centavos'] - $saldoContabil;
            if ($diferenca !== 0 && mb_strlen(trim((string) ($dados['justificativa'] ?? ''))) < 10) {
                throw ValidationException::withMessages(['justificativa' => 'Justifique a diferença entre os saldos com pelo menos 10 caracteres.']);
            }

            $gestao->update([
                'saldo_real_final_centavos' => $dados['saldo_real_final_centavos'],
                'diferenca_conciliacao_centavos' => $diferenca,
                'justificativa_conciliacao' => $dados['justificativa'] ?? null,
                'conciliado_em' => now(),
                'conciliado_por' => $request->user()->id,
            ]);
            Auditoria::registrar('conciliou_saldo_financeiro', descricao: 'Saldo contábil: '.$saldoContabil.'; saldo real: '.$dados['saldo_real_final_centavos'].'; diferença: '.$diferenca.'; justificativa: '.($dados['justificativa'] ?? ''), entidade: 'gestao_financeira', entidadeId: $gestao->id);

            return ['saldo_contabil_centavos' => $saldoContabil, 'saldo_real_final_centavos' => (int) $dados['saldo_real_final_centavos'], 'diferenca_centavos' => $diferenca];
        });

        return response()->json($resultado);
    }
}

<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Services\AutorizacaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelamentoMovimentacaoFinanceiraController extends Controller
{
    public function cancelar(Request $request, MovimentacaoFinanceira $movimentacao, AutorizacaoFinanceira $autorizacao)
    {
        $dados = $request->validate(['justificativa' => ['required', 'string', 'min:10']]);
        DB::transaction(function () use ($request, $movimentacao, $autorizacao, $dados) {
            $gestao = GestaoFinanceira::whereKey($movimentacao->gestao_financeira_id)->lockForUpdate()->firstOrFail();
            abort_unless($autorizacao->podeOperar($request->user(), $gestao), 403);
            $registro = MovimentacaoFinanceira::whereKey($movimentacao->id)->lockForUpdate()->firstOrFail();
            if ($registro->status !== 'pendente' || RelatorioFinanceiro::where('gestao_financeira_id', $gestao->id)
                ->where('ano', $registro->data->year)->where('mes', $registro->data->month)
                ->whereIn('status', ['fechado', 'publicado'])->exists()) {
                throw ValidationException::withMessages(['movimentacao' => 'Não é possível cancelar esta movimentação.']);
            }
            $registro->update(['status' => 'cancelada']);
            Auditoria::registrar('cancelou_movimentacao_financeira', descricao: $dados['justificativa'],
                entidade: 'movimentacao_financeira', entidadeId: $registro->id);
        });
        return response()->noContent();
    }
}

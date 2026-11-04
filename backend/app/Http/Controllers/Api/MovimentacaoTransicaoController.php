<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimentacaoTransicaoController extends Controller
{
    public function index(int $transicao)
    {
        abort_unless(DB::table('transicoes_financeiras')->where('id', $transicao)->exists(), 404);
        return DB::table('movimentacoes_transicao_financeira')
            ->where('transicao_financeira_id', $transicao)->orderByDesc('data')->paginate(25);
    }

    public function criar(Request $request, int $transicao)
    {
        $dados = $request->validate([
            'data' => ['required', 'date'],
            'tipo' => ['required', 'in:receita,despesa'],
            'categoria' => ['required', 'string', 'max:100'],
            'descricao' => ['required', 'string', 'max:255'],
            'justificativa_essencialidade' => ['required', 'string', 'min:10', 'max:3000'],
            'valor_centavos' => ['required', 'integer', 'min:1'],
        ]);
        $id = DB::transaction(function () use ($request, $transicao, $dados) {
            $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)->lockForUpdate()->first();
            abort_unless($periodo, 404);
            if ($periodo->status !== 'ativa' || $dados['data'] < $periodo->inicio || $dados['data'] > $periodo->fim_previsto) {
                throw ValidationException::withMessages(['data' => 'Data fora da transição ativa.']);
            }
            $id = DB::table('movimentacoes_transicao_financeira')->insertGetId([
                ...$dados, 'transicao_financeira_id' => $transicao,
                'status' => 'pendente', 'criada_por' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            Auditoria::registrar('criou_movimentacao_transicao', entidade: 'movimentacao_transicao_financeira', entidadeId: $id);
            return $id;
        });
        return response()->json(DB::table('movimentacoes_transicao_financeira')->find($id), 201);
    }

    public function resolver(Request $request, int $transicao, int $movimentacao)
    {
        $dados = $request->validate([
            'acao' => ['required', 'in:confirmar,cancelar'],
            'justificativa' => ['required_if:acao,cancelar', 'nullable', 'string', 'min:10', 'max:2000'],
        ]);
        DB::transaction(function () use ($request, $transicao, $movimentacao, $dados) {
            $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)->lockForUpdate()->first();
            abort_unless($periodo, 404);
            $registro = DB::table('movimentacoes_transicao_financeira')
                ->where('transicao_financeira_id', $transicao)->where('id', $movimentacao)->lockForUpdate()->first();
            abort_unless($registro, 404);
            if ($periodo->status !== 'ativa' || $registro->status !== 'pendente') {
                throw ValidationException::withMessages(['movimentacao' => 'Movimentação não está pendente.']);
            }
            DB::table('movimentacoes_transicao_financeira')->where('id', $movimentacao)->update([
                'status' => $dados['acao'] === 'confirmar' ? 'confirmada' : 'cancelada',
                'justificativa_cancelamento' => $dados['justificativa'] ?? null,
                'resolvida_por' => $request->user()->id, 'updated_at' => now(),
            ]);
            Auditoria::registrar('resolveu_movimentacao_transicao', descricao: $dados['acao'], entidade: 'movimentacao_transicao_financeira', entidadeId: $movimentacao);
        });
        return response()->noContent();
    }
}

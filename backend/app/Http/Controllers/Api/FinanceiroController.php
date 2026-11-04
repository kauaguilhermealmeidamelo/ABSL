<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Services\FinanceiroService;
use App\Services\AutorizacaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FinanceiroController extends Controller
{
    public function gestoes()
    {
        return GestaoFinanceira::query()->orderByDesc('inicio')->get(['id', 'nome', 'inicio', 'fim_previsto', 'status', 'encerrada_em', 'saldo_inicial_centavos']);
    }

    public function resumo(Request $request, GestaoFinanceira $gestao, FinanceiroService $financeiro, AutorizacaoFinanceira $autorizacao)
    {
        abort_unless($autorizacao->podeConsultarHistorico($request->user(), $gestao), 403);
        return response()->json(['gestao' => $gestao->only(['id', 'nome', 'status', 'inicio', 'encerrada_em']), 'totais' => $financeiro->totais($gestao)]);
    }

    public function movimentacoes(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao)
    {
        abort_unless($autorizacao->podeConsultarHistorico($request->user(), $gestao), 403);
        return $gestao->movimentacoes()->orderByDesc('data')->orderByDesc('id')->paginate(25);
    }

    public function criarMovimentacao(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao)
    {
        abort_unless($autorizacao->podeOperar($request->user(), $gestao), 403);
        $data = $request->validate([
            'data' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:' . $gestao->inicio->toDateString()],
            'tipo' => ['required', Rule::in(['receita', 'despesa'])],
            'categoria' => ['required', 'string', 'max:100'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor_centavos' => ['required', 'integer', 'min:1'],
        ]);

        $movimentacao = DB::transaction(function () use ($gestao, $data, $request, $autorizacao) {
            $bloqueada = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            abort_unless($autorizacao->podeOperar($request->user(), $bloqueada), 403);
            if ($bloqueada->status !== 'ativa') {
                throw ValidationException::withMessages(['gestao' => 'Gestão encerrada.']);
            }
            if ($bloqueada->fim_previsto && $data['data'] > $bloqueada->fim_previsto->toDateString()) {
                throw ValidationException::withMessages(['data' => 'Data posterior ao término da gestão.']);
            }
            if (\App\Models\RelatorioFinanceiro::where('gestao_financeira_id', $bloqueada->id)
                ->where('ano', (int) substr($data['data'], 0, 4))
                ->where('mes', (int) substr($data['data'], 5, 2))
                ->whereIn('status', ['fechado', 'publicado'])->exists()) {
                throw ValidationException::withMessages(['data' => 'Este mês já possui relatório publicado.']);
            }
            $movimentacao = $bloqueada->movimentacoes()->create([...$data, 'status' => 'pendente', 'criada_por' => $request->user()->id]);
            Auditoria::registrar('criou_movimentacao_financeira', entidade: 'movimentacao_financeira', entidadeId: $movimentacao->id);
            return $movimentacao;
        });

        return response()->json($movimentacao, 201);
    }

    public function confirmarMovimentacao(Request $request, MovimentacaoFinanceira $movimentacao, AutorizacaoFinanceira $autorizacao)
    {
        DB::transaction(function () use ($movimentacao, $request, $autorizacao) {
            $gestao = GestaoFinanceira::whereKey($movimentacao->gestao_financeira_id)->lockForUpdate()->firstOrFail();
            abort_unless($autorizacao->podeOperar($request->user(), $gestao), 403);
            $registro = MovimentacaoFinanceira::whereKey($movimentacao->id)->lockForUpdate()->firstOrFail();
            if ($gestao->status !== 'ativa' || $registro->status !== 'pendente'
                || \App\Models\RelatorioFinanceiro::where('gestao_financeira_id', $gestao->id)
                    ->where('ano', (int) $registro->data->format('Y'))
                    ->where('mes', (int) $registro->data->format('m'))
                    ->whereIn('status', ['fechado', 'publicado'])->exists()) {
                throw ValidationException::withMessages(['movimentacao' => 'Movimentação não pode ser confirmada.']);
            }
            $registro->update(['status' => 'confirmada']);
            Auditoria::registrar('confirmou_movimentacao_financeira', entidade: 'movimentacao_financeira', entidadeId: $registro->id);
        });

        return response()->noContent();
    }

    public function encerrar(Request $request, GestaoFinanceira $gestao, FinanceiroService $financeiro)
    {
        $data = $request->validate(['observacao' => ['required', 'string', 'min:10', 'max:2000']]);
        $totais = $financeiro->encerrar($gestao, $request->user()->id, $data['observacao']);
        return response()->json(['totais' => $totais, 'status' => 'encerrada']);
    }
}

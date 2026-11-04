<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RelatorioFinanceiroController extends Controller
{
    public function index()
    {
        return RelatorioFinanceiro::query()->where('status', 'publicado')
            ->orderByDesc('ano')->orderByDesc('mes')
            ->get(['id', 'gestao_financeira_id', 'ano', 'mes', 'saldo_inicial_centavos',
                'receitas_centavos', 'despesas_centavos', 'saldo_final_centavos', 'publicado_em']);
    }

    public function publicar(Request $request, GestaoFinanceira $gestao)
    {
        $dados = $request->validate([
            'ano' => ['required', 'integer', 'between:2000,2100'],
            'mes' => ['required', 'integer', 'between:1,12'],
        ]);
        $ano = (int) $dados['ano'];
        $mes = (int) $dados['mes'];
        $inicio = sprintf('%04d-%02d-01', $ano, $mes);
        $fim = date('Y-m-d', strtotime($inicio . ' +1 month'));

        $relatorio = DB::transaction(function () use ($gestao, $request, $ano, $mes, $inicio, $fim) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if ($gestao->status !== 'ativa' || $fim > today()->toDateString()
                || $fim <= $gestao->inicio->toDateString()) {
                throw ValidationException::withMessages(['periodo' => 'Somente meses concluídos dentro da gestão podem ser publicados.']);
            }
            if ($gestao->movimentacoes()->where('status', 'pendente')
                ->where('data', '>=', $inicio)->where('data', '<', $fim)->exists()) {
                throw ValidationException::withMessages(['periodo' => 'Existem movimentações pendentes.']);
            }
            if (RelatorioFinanceiro::where('gestao_financeira_id', $gestao->id)
                ->where('ano', $ano)->where('mes', $mes)->exists()) {
                throw ValidationException::withMessages(['periodo' => 'Relatório já publicado.']);
            }

            $somas = function ($query) {
                return $query->where('status', 'confirmada')->selectRaw('tipo, SUM(valor_centavos) as total')
                    ->groupBy('tipo')->pluck('total', 'tipo');
            };
            $anteriores = $somas($gestao->movimentacoes()->where('data', '<', $inicio));
            $atuais = $somas($gestao->movimentacoes()->where('data', '>=', $inicio)->where('data', '<', $fim));
            $saldo = (int) $gestao->saldo_inicial_centavos
                + (int) ($anteriores['receita'] ?? 0) - (int) ($anteriores['despesa'] ?? 0);
            $receitas = (int) ($atuais['receita'] ?? 0);
            $despesas = (int) ($atuais['despesa'] ?? 0);
            $relatorio = RelatorioFinanceiro::create([
                'gestao_financeira_id' => $gestao->id, 'ano' => $ano, 'mes' => $mes,
                'status' => 'publicado', 'saldo_inicial_centavos' => $saldo,
                'receitas_centavos' => $receitas, 'despesas_centavos' => $despesas,
                'saldo_final_centavos' => $saldo + $receitas - $despesas,
                'publicado_em' => now(), 'publicado_por' => $request->user()->id,
            ]);
            Auditoria::registrar('publicou_relatorio_financeiro', entidade: 'relatorio_financeiro', entidadeId: $relatorio->id);
            return $relatorio;
        });
        return response()->json($relatorio, 201);
    }
}

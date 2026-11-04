<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use Illuminate\Support\Facades\DB;

class TransparenciaFinanceiraPublicaController extends Controller
{
    public function gestoes()
    {
        return GestaoFinanceira::query()
            ->whereIn('status', ['ativa', 'encerrada'])
            ->orderByDesc('inicio')
            ->get(['id', 'nome', 'inicio', 'fim_previsto', 'status', 'encerrada_em',
                'saldo_inicial_centavos', 'saldo_real_final_centavos', 'diferenca_conciliacao_centavos']);
    }

    public function relatorios(GestaoFinanceira $gestao)
    {
        return DB::table('relatorios_financeiros')
            ->where('gestao_financeira_id', $gestao->id)->where('status', 'publicado')
            ->orderBy('ano')->orderBy('mes')
            ->get(['id', 'ano', 'mes', 'versao', 'saldo_inicial_centavos',
                'receitas_centavos', 'despesas_centavos', 'saldo_final_centavos', 'publicado_em']);
    }

    public function versoes(int $relatorio)
    {
        abort_unless(DB::table('relatorios_financeiros')->where('id', $relatorio)
            ->where('status', 'publicado')->exists(), 404);

        return DB::table('versoes_relatorios_financeiros')
            ->where('relatorio_financeiro_id', $relatorio)
            ->orderBy('versao')
            ->get(['versao', 'saldo_inicial_centavos', 'receitas_centavos',
                'despesas_centavos', 'saldo_final_centavos', 'justificativa', 'publicado_em']);
    }
}

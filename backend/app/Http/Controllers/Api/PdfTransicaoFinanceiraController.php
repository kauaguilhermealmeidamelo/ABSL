<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PdfFinanceiroSimples;
use Illuminate\Support\Facades\DB;

class PdfTransicaoFinanceiraController extends Controller
{
    public function download(int $transicao, PdfFinanceiroSimples $pdf)
    {
        $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)
            ->where('status', 'encerrada')->whereNotNull('relatorio_publicado_em')->first();
        abort_unless($periodo, 404);
        if ($periodo->pdf_base64) {
            return response(base64_decode($periodo->pdf_base64), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="transicao-'.$transicao.'.pdf"',
            ]);
        }

        $movimentos = DB::table('movimentacoes_transicao_financeira')
            ->where('transicao_financeira_id', $transicao)->orderBy('data')->get();
        $receitas = $movimentos->where('status', 'confirmada')->where('tipo', 'receita')->sum('valor_centavos');
        $despesas = $movimentos->where('status', 'confirmada')->where('tipo', 'despesa')->sum('valor_centavos');
        $linhas = [
            'GREMIO ESTUDANTIL ATHOS BULCAO - ABSL',
            'RELATORIO CONSOLIDADO DE TRANSICAO ADMINISTRATIVA',
            'Periodo: '.$periodo->inicio.' a '.$periodo->fim_real,
            'Motivo: '.$periodo->motivo,
            'Responsavel administrativo: usuario #'.$periodo->responsavel_admin_id,
            'Saldo inicial: '.$periodo->saldo_inicial_centavos.' centavos',
            'Receitas confirmadas: '.$receitas.' centavos',
            'Despesas confirmadas: '.$despesas.' centavos',
            'Saldo contabil: '.($periodo->saldo_inicial_centavos + $receitas - $despesas).' centavos',
            'Saldo real conciliado: '.$periodo->saldo_real_final_centavos.' centavos',
            'Diferenca: '.$periodo->diferenca_conciliacao_centavos.' centavos',
            'Justificativa: '.$periodo->justificativa_conciliacao,
            'Publicado por usuario #'.$periodo->relatorio_publicado_por.' em '.$periodo->relatorio_publicado_em,
            'MOVIMENTACOES:',
        ];
        foreach ($movimentos as $movimento) {
            $linhas[] = '#'.$movimento->id.' '.$movimento->data.' '.$movimento->tipo
                .' '.$movimento->status.' '.$movimento->valor_centavos.' centavos - '.$movimento->descricao;
            $linhas[] = 'Essencialidade: '.$movimento->justificativa_essencialidade;
        }
        return response($pdf->gerar($linhas), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="transicao-'.$transicao.'.pdf"',
        ]);
    }
}

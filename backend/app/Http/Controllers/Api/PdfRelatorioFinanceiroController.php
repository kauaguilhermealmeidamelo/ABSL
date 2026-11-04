<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Services\PdfFinanceiroSimples;

class PdfRelatorioFinanceiroController extends Controller
{
    public function versao(RelatorioFinanceiro $relatorio, int $versao)
    {
        abort_unless($relatorio->status === 'publicado', 404);
        $registro = \Illuminate\Support\Facades\DB::table('versoes_relatorios_financeiros')
            ->where('relatorio_financeiro_id', $relatorio->id)->where('versao', $versao)->first();
        abort_unless($registro, 404);
        return response(base64_decode($registro->pdf_base64), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="prestacao-'.$relatorio->id.'-v'.$versao.'.pdf"',
        ]);
    }

    public function download(RelatorioFinanceiro $relatorio, PdfFinanceiroSimples $pdf)
    {
        abort_unless($relatorio->status === 'publicado', 404);
        $versao = \Illuminate\Support\Facades\DB::table('versoes_relatorios_financeiros')
            ->where('relatorio_financeiro_id', $relatorio->id)
            ->where('versao', $relatorio->versao)->first();
        if ($versao) {
            return response(base64_decode($versao->pdf_base64), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="prestacao-'.$relatorio->id.'-v'.$versao->versao.'.pdf"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
        $gestao = GestaoFinanceira::findOrFail($relatorio->gestao_financeira_id);

        $linhas = [
            'GREMIO ESTUDANTIL ATHOS BULCAO - ABSL',
            'PRESTACAO DE CONTAS MENSAL',
            'Gestao: '.$gestao->nome,
            'Competencia: '.sprintf('%02d/%04d', $relatorio->mes, $relatorio->ano),
            'Versao: '.($relatorio->versao ?? 1),
            'Saldo inicial (centavos): '.$relatorio->saldo_inicial_centavos,
            'Receitas confirmadas (centavos): '.$relatorio->receitas_centavos,
            'Despesas confirmadas (centavos): '.$relatorio->despesas_centavos,
            'Saldo final contabil (centavos): '.$relatorio->saldo_final_centavos,
            'Fechado por usuario #'.$relatorio->fechado_por.' em '.$relatorio->fechado_em,
            'Aprovado e publicado por usuario #'.$relatorio->publicado_por.' em '.$relatorio->publicado_em,
            'Comprovantes privados nao integram este documento.',
        ];
        $inicio = sprintf('%04d-%02d-01', $relatorio->ano, $relatorio->mes);
        $fim = date('Y-m-d', strtotime($inicio.' +1 month'));
        $movimentacoes = $gestao->movimentacoes()->where('data', '>=', $inicio)
            ->where('data', '<', $fim)->orderBy('data')->orderBy('id')->get();
        $linhas[] = 'MOVIMENTACOES DO PERIODO';
        foreach ($movimentacoes as $movimentacao) {
            $linhas[] = $movimentacao->data->format('d/m/Y').' - '.$movimentacao->tipo
                .' - '.$movimentacao->status.' - '.$movimentacao->valor_centavos
                .' centavos - '.$movimentacao->descricao;
        }
        $conteudo = $pdf->gerar($linhas);

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="prestacao-'.$relatorio->id.'.pdf"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

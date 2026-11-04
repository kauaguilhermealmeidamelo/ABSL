<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Services\PdfFinanceiroSimples;

class PdfRelatorioFinanceiroController extends Controller
{
    public function download(RelatorioFinanceiro $relatorio, PdfFinanceiroSimples $pdf)
    {
        abort_unless($relatorio->status === 'publicado', 404);
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
        $conteudo = $pdf->gerar($linhas);

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="prestacao-'.$relatorio->id.'.pdf"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

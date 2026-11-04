<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Services\PdfFinanceiroSimples;
use Illuminate\Support\Facades\DB;

class PdfInatividadeController extends Controller
{
    public function download(GestaoFinanceira $gestao, PdfFinanceiroSimples $pdf)
    {
        $declaracao = DB::table('declaracoes_inatividade_financeira')
            ->where('gestao_financeira_id', $gestao->id)
            ->where('status', 'publicada')->first();
        abort_unless($declaracao, 404);

        $canceladas = $gestao->movimentacoes()->where('status', 'cancelada')->get();
        $nomeTesouraria = \App\Models\User::find($declaracao->aprovada_tesouraria_por)?->name ?? 'Não identificado';
        $nomeAdmin = \App\Models\User::find($declaracao->aprovada_admin_por)?->name ?? 'Não identificado';
        $linhas = [
            'GREMIO ESTUDANTIL ATHOS BULCAO - ABSL',
            'DECLARACAO DE INATIVIDADE FINANCEIRA',
            'Gestao: '.$gestao->nome,
            'Periodo: '.$gestao->inicio->format('d/m/Y').' a '.$gestao->fim_previsto?->format('d/m/Y'),
            'Saldo inicial (centavos): '.$gestao->saldo_inicial_centavos,
            'Saldo contabil final (centavos): '.$gestao->saldo_inicial_centavos,
            'Saldo real conciliado (centavos): '.$gestao->saldo_real_final_centavos,
            'Diferenca de conciliacao (centavos): '.$gestao->diferenca_conciliacao_centavos,
            'Justificativa conciliacao: '.$gestao->justificativa_conciliacao,
            'Nao houve receitas ou despesas confirmadas durante esta gestao.',
            'Observacoes: '.$declaracao->observacoes,
            'Aprovacao tesouraria: '.$declaracao->cargo_aprovador_tesouraria.' - '.$nomeTesouraria,
            'Aprovacao administracao: '.$nomeAdmin,
            'Publicada em: '.$declaracao->publicada_em,
            'Movimentacoes canceladas: '.$canceladas->count(),
        ];
        foreach ($canceladas as $movimentacao) {
            $linhas[] = '#'.$movimentacao->id.' '.$movimentacao->data->format('d/m/Y').' '.$movimentacao->tipo
                .' '.$movimentacao->valor_centavos.' centavos - '.$movimentacao->descricao;
        }
        $conteudo = $pdf->gerar($linhas);
        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="declaracao-inatividade-'.$gestao->id.'.pdf"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}

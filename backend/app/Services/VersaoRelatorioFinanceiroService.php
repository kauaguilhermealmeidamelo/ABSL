<?php

namespace App\Services;

use App\Models\GestaoFinanceira;
use App\Models\RelatorioFinanceiro;
use Illuminate\Support\Facades\DB;

class VersaoRelatorioFinanceiroService
{
    public function registrar(RelatorioFinanceiro $relatorio, int $usuarioId, ?string $justificativa = null): void
    {
        $gestao = GestaoFinanceira::findOrFail($relatorio->gestao_financeira_id);
        $inicio = sprintf('%04d-%02d-01', $relatorio->ano, $relatorio->mes);
        $fim = date('Y-m-d', strtotime($inicio.' +1 month'));
        $linhas = [
            'GREMIO ESTUDANTIL ATHOS BULCAO - ABSL',
            'PRESTACAO DE CONTAS MENSAL - VERSAO '.$relatorio->versao,
            'Gestao: '.$gestao->nome,
            'Competencia: '.sprintf('%02d/%04d', $relatorio->mes, $relatorio->ano),
            'Saldo inicial: '.$relatorio->saldo_inicial_centavos.' centavos',
            'Receitas: '.$relatorio->receitas_centavos.' centavos',
            'Despesas: '.$relatorio->despesas_centavos.' centavos',
            'Saldo final: '.$relatorio->saldo_final_centavos.' centavos',
            'Publicado por usuario #'.$usuarioId.' em '.now()->toDateTimeString(),
            'Justificativa da versao: '.$justificativa,
            'Comprovantes privados nao sao publicados.',
            'MOVIMENTACOES:',
        ];
        foreach ($gestao->movimentacoes()->where('data', '>=', $inicio)
            ->where('data', '<', $fim)->orderBy('data')->orderBy('id')->get() as $movimento) {
            $linhas[] = '#'.$movimento->id.' '.$movimento->data->format('d/m/Y')
                .' '.$movimento->tipo.' '.$movimento->status.' '.$movimento->valor_centavos
                .' centavos '.$movimento->descricao;
        }
        $pdf = app(PdfFinanceiroSimples::class)->gerar($linhas);
        DB::table('versoes_relatorios_financeiros')->insert([
            'relatorio_financeiro_id' => $relatorio->id,
            'versao' => $relatorio->versao,
            'saldo_inicial_centavos' => $relatorio->saldo_inicial_centavos,
            'receitas_centavos' => $relatorio->receitas_centavos,
            'despesas_centavos' => $relatorio->despesas_centavos,
            'saldo_final_centavos' => $relatorio->saldo_final_centavos,
            'justificativa' => $justificativa,
            'pdf_base64' => base64_encode($pdf),
            'publicado_por' => $usuarioId,
            'publicado_em' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}

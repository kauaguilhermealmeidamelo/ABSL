<?php

namespace App\Services;

use App\Models\GestaoFinanceira;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceiroService
{
    public function totais(GestaoFinanceira $gestao): array
    {
        $somas = $gestao->movimentacoes()
            ->where('status', 'confirmada')
            ->selectRaw("tipo, SUM(valor_centavos) as total")
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $receitas = (int) ($somas['receita'] ?? 0);
        $despesas = (int) ($somas['despesa'] ?? 0);

        return [
            'saldo_inicial_centavos' => (int) $gestao->saldo_inicial_centavos,
            'receitas_centavos' => $receitas,
            'despesas_centavos' => $despesas,
            'saldo_final_centavos' => (int) $gestao->saldo_inicial_centavos + $receitas - $despesas,
        ];
    }

    public function encerrar(GestaoFinanceira $gestao, int $usuarioId, string $observacao): array
    {
        return DB::transaction(function () use ($gestao, $usuarioId, $observacao) {
            $bloqueada = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if ($bloqueada->status !== 'ativa') {
                throw ValidationException::withMessages(['gestao' => 'A gestão já está encerrada.']);
            }
            if ($bloqueada->movimentacoes()->where('status', 'pendente')->exists()) {
                throw ValidationException::withMessages(['gestao' => 'Confirme ou regularize as movimentações pendentes antes de encerrar.']);
            }

            if ($bloqueada->saldo_real_final_centavos === null || $bloqueada->conciliado_em === null) {
                throw ValidationException::withMessages(['gestao' => 'Concilie o saldo real antes do encerramento definitivo.']);
            }

            $totais = $this->totais($bloqueada);
            if ((int) $bloqueada->saldo_real_final_centavos - $totais['saldo_final_centavos']
                !== (int) $bloqueada->diferenca_conciliacao_centavos) {
                throw ValidationException::withMessages(['gestao' => 'A conciliação está desatualizada.']);
            }

            $primeiro = $bloqueada->inicio->copy()->startOfMonth();
            $ultimo = $bloqueada->fim_previsto
                ? $bloqueada->fim_previsto->copy()->startOfMonth()
                : today()->startOfMonth();
            if ($ultimo->greaterThan(today()->startOfMonth())) {
                throw ValidationException::withMessages(['gestao' => 'O período previsto da gestão ainda não terminou.']);
            }

            for ($mes = $primeiro; $mes->lessThanOrEqualTo($ultimo); $mes->addMonth()) {
                $publicado = \App\Models\RelatorioFinanceiro::where('gestao_financeira_id', $bloqueada->id)
                    ->where('ano', $mes->year)->where('mes', $mes->month)
                    ->where('status', 'publicado')->exists();
                if (! $publicado) {
                    throw ValidationException::withMessages(['gestao' => 'Publique o relatório de '.$mes->format('Y-m').' antes de encerrar.']);
                }
            }

            if (! $bloqueada->movimentacoes()->where('status', 'confirmada')->exists()) {
                throw ValidationException::withMessages(['gestao' => 'Gestão sem movimentações: publique a declaração de inatividade com dupla aprovação antes de encerrar.']);
            }
            $bloqueada->update([
                'status' => 'encerrada',
                'encerrada_em' => today(),
                'encerrada_por' => $usuarioId,
                'observacao_encerramento' => $observacao,
            ]);

            return $totais;
        });
    }
}

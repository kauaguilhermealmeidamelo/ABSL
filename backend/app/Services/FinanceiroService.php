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

            $totais = $this->totais($bloqueada);
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

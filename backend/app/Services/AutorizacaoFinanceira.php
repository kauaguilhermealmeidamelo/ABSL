<?php

namespace App\Services;

use App\Models\GestaoFinanceira;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AutorizacaoFinanceira
{
    public function podeOperar(User $usuario, GestaoFinanceira $gestao): bool
    {
        if ($gestao->status !== 'ativa') {
            return false;
        }

        if ($usuario->isAdmin()) {
            return true;
        }

        return $this->possuiCargo($usuario, $gestao, ['tesoureiro', 'tesoureiro_geral']);
    }

    public function podeGerenciarFechamento(User $usuario, GestaoFinanceira $gestao): bool
    {
        if ($gestao->status !== 'ativa') {
            return false;
        }

        return $usuario->isAdmin()
            || $this->possuiCargo($usuario, $gestao, ['tesoureiro_geral']);
    }

    public function podeEncerrarDefinitivamente(User $usuario, GestaoFinanceira $gestao): bool
    {
        return $usuario->isAdmin() && $gestao->status === 'ativa';
    }

    private function possuiCargo(User $usuario, GestaoFinanceira $gestao, array $cargos): bool
    {
        $agora = now();

        $ativos = DB::table('vinculos_financeiros')
            ->where('gestao_financeira_id', $gestao->id)
            ->whereNull('fim_em')
            ->where('inicio_em', '<=', $agora)
            ->where(function ($query) use ($agora) {
                $query->whereNull('fim_previsto_em')
                    ->orWhere('fim_previsto_em', '>', $agora);
            });

        // Durante a interinidade, o titular mantém seu vínculo histórico,
        // mas não exerce as permissões superiores.
        $interinoAtivo = (clone $ativos)
            ->where('cargo', 'tesoureiro_geral')
            ->where('interino', true)
            ->exists();

        return $ativos
            ->where('user_id', $usuario->id)
            ->whereIn('cargo', $cargos)
            ->when(
                $interinoAtivo && in_array('tesoureiro_geral', $cargos, true),
                fn ($query) => $query->where(function ($q) {
                    $q->where('cargo', 'tesoureiro')
                        ->orWhere('interino', true);
                })
            )
            ->exists();
    }
}

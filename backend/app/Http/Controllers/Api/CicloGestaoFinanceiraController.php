<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CicloGestaoFinanceiraController extends Controller
{
    public function ativar(Request $request, GestaoFinanceira $gestao)
    {
        $resultado = DB::transaction(function () use ($request, $gestao) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();

            if ($gestao->status !== 'planejada') {
                throw ValidationException::withMessages(['gestao' => 'Somente gestões planejadas podem ser ativadas.']);
            }
            if (GestaoFinanceira::where('status', 'ativa')->whereKeyNot($gestao->id)->exists()) {
                throw ValidationException::withMessages(['gestao' => 'Já existe uma gestão ativa.']);
            }

            $anterior = GestaoFinanceira::where('inicio', '<', $gestao->inicio)
                ->orderByDesc('inicio')->lockForUpdate()->first();

            if ($anterior && ($anterior->status !== 'encerrada' || $anterior->saldo_real_final_centavos === null)) {
                throw ValidationException::withMessages(['gestao' => 'A gestão anterior precisa estar encerrada e conciliada.']);
            }

            $transicao = DB::table('transicoes_financeiras')->where('inicio', '<', $gestao->inicio->toDateString())
                ->orderByDesc('inicio')->first();
            if ($transicao && $transicao->status !== 'encerrada') {
                throw ValidationException::withMessages(['gestao' => 'Encerre a transição antes de ativar a gestão.']);
            }
            $saldoInicial = $transicao ? (int) $transicao->saldo_real_final_centavos
                : ($anterior ? (int) $anterior->saldo_real_final_centavos : 0);
            $gestao->update([
                'status' => 'ativa',
                'saldo_inicial_centavos' => $saldoInicial,
                'ativada_em' => now(),
                'ativada_por' => $request->user()->id,
            ]);

            Auditoria::registrar('ativou_gestao_financeira', entidade: 'gestao_financeira', entidadeId: $gestao->id);
            return $gestao;
        });

        return response()->json($resultado);
    }

    public function planejar(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'inicio' => ['required', 'date'],
            'fim_previsto' => ['required', 'date', 'after_or_equal:inicio'],
        ]);

        $gestao = DB::transaction(function () use ($request, $dados) {
            if (GestaoFinanceira::where('inicio', '<=', $dados['fim_previsto'])
                ->where(fn ($q) => $q->whereNull('fim_previsto')
                    ->orWhere('fim_previsto', '>=', $dados['inicio']))->exists()) {
                throw ValidationException::withMessages(['inicio' => 'Período de gestão sobreposto.']);
            }

            $gestao = GestaoFinanceira::create([
                ...$dados,
                'status' => 'planejada',
                'saldo_inicial_centavos' => 0,
                'criada_por' => $request->user()->id,
            ]);

            Auditoria::registrar('planejou_gestao_financeira', entidade: 'gestao_financeira', entidadeId: $gestao->id);
            return $gestao;
        });

        return response()->json($gestao, 201);
    }
}

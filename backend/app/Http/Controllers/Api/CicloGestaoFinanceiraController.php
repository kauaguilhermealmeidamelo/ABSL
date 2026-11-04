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
        $dados = $request->validate([
            'saldo_inicial_centavos' => ['sometimes', 'integer', 'min:0'],
            'justificativa_saldo_inicial' => ['nullable', 'string', 'max:3000'],
        ]);
        $resultado = DB::transaction(function () use ($request, $gestao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();

            if ($gestao->status !== 'planejada') {
                throw ValidationException::withMessages(['gestao' => 'Somente gestões planejadas podem ser ativadas.']);
            }
            if ($gestao->inicio->isFuture() || $gestao->fim_previsto->isPast()) {
                throw ValidationException::withMessages(['gestao' => 'Ative a gestão apenas dentro do período previsto.']);
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
            $transicaoPosterior = $transicao && (! $anterior || $transicao->inicio > $anterior->inicio->toDateString());
            $saldoInicial = $transicaoPosterior ? (int) $transicao->saldo_real_final_centavos
                : ($anterior ? (int) $anterior->saldo_real_final_centavos : 0);
            if (! $anterior && ! $transicaoPosterior) {
                $saldoInicial = (int) ($dados['saldo_inicial_centavos'] ?? 0);
                if ($saldoInicial !== 0 && mb_strlen(trim($dados['justificativa_saldo_inicial'] ?? '')) < 10) {
                    throw ValidationException::withMessages(['justificativa_saldo_inicial' => 'Justifique o saldo inicial excepcional.']);
                }
            } elseif (array_key_exists('saldo_inicial_centavos', $dados)) {
                throw ValidationException::withMessages(['saldo_inicial_centavos' => 'O saldo deve ser herdado do período anterior.']);
            }

            $titulares = DB::table('vinculos_financeiros')
                ->where('gestao_financeira_id', $gestao->id)
                ->where('cargo', 'tesoureiro_geral')->where('interino', false)
                ->whereNull('fim_em')->count();
            if ($titulares !== 1) {
                throw ValidationException::withMessages(['gestao' => 'A gestão precisa de exatamente um Tesoureiro-Geral titular.']);
            }
            $gestao->update([
                'status' => 'ativa',
                'saldo_inicial_centavos' => $saldoInicial,
                'justificativa_saldo_inicial' => $dados['justificativa_saldo_inicial'] ?? null,
                'ativada_em' => now(),
                'ativada_por' => $request->user()->id,
            ]);

            Auditoria::registrar('ativou_gestao_financeira', entidade: 'gestao_financeira', entidadeId: $gestao->id);
            return $gestao;
        });

        return response()->json($resultado);
    }

    public function ajustarDatas(Request $request, GestaoFinanceira $gestao)
    {
        $dados = $request->validate([
            'inicio' => ['required', 'date'],
            'fim_previsto' => ['required', 'date', 'after_or_equal:inicio'],
            'justificativa' => ['required', 'string', 'min:10', 'max:3000'],
        ]);
        $resultado = DB::transaction(function () use ($request, $gestao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão encerrada não pode ser alterada.']);
            }
            if (GestaoFinanceira::whereKeyNot($gestao->id)
                ->where('inicio', '<=', $dados['fim_previsto'])
                ->where(fn ($q) => $q->whereNull('fim_previsto')->orWhere('fim_previsto', '>=', $dados['inicio']))
                ->exists()) {
                throw ValidationException::withMessages(['inicio' => 'Datas sobrepostas a outra gestão.']);
            }
            if ($gestao->movimentacoes()->where(fn ($q) => $q->where('data', '<', $dados['inicio'])
                ->orWhere('data', '>', $dados['fim_previsto']))->exists()) {
                throw ValidationException::withMessages(['inicio' => 'Existem movimentações fora do período proposto.']);
            }
            $antigo = $gestao->inicio->toDateString().' - '.$gestao->fim_previsto?->toDateString();
            $gestao->update(['inicio' => $dados['inicio'], 'fim_previsto' => $dados['fim_previsto']]);
            Auditoria::registrar('ajustou_datas_gestao_financeira',
                descricao: 'Período anterior: '.$antigo.'; novo: '.$dados['inicio'].' - '.$dados['fim_previsto'].'; '.$dados['justificativa'],
                entidade: 'gestao_financeira', entidadeId: $gestao->id);
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

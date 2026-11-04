<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransicaoFinanceiraController extends Controller
{
    public function index()
    {
        return DB::table('transicoes_financeiras')->orderByDesc('inicio')->get();
    }

    public function ajustar(Request $request, int $transicao)
    {
        $dados = $request->validate([
            'fim_previsto' => ['required', 'date'],
            'justificativa' => ['required', 'string', 'min:10', 'max:3000'],
        ]);
        $registro = DB::transaction(function () use ($request, $transicao, $dados) {
            $periodo = DB::table('transicoes_financeiras')->where('id', $transicao)->lockForUpdate()->first();
            abort_unless($periodo, 404);
            if ($periodo->status !== 'ativa' || $dados['fim_previsto'] < $periodo->inicio) {
                throw ValidationException::withMessages(['fim_previsto' => 'Data inválida para transição ativa.']);
            }
            if (DB::table('movimentacoes_transicao_financeira')
                ->where('transicao_financeira_id', $transicao)
                ->where('data', '>', $dados['fim_previsto'])->exists()) {
                throw ValidationException::withMessages(['fim_previsto' => 'Existem movimentações posteriores ao novo término.']);
            }
            if (GestaoFinanceira::where('inicio', '<=', $dados['fim_previsto'])
                ->where(fn ($q) => $q->whereNull('fim_previsto')->orWhere('fim_previsto', '>=', $periodo->inicio))
                ->exists()) {
                throw ValidationException::withMessages(['fim_previsto' => 'Período sobreposto a uma gestão.']);
            }
            DB::table('transicoes_financeiras')->where('id', $transicao)->update([
                'fim_previsto' => $dados['fim_previsto'], 'updated_at' => now(),
            ]);
            Auditoria::registrar('ajustou_transicao_financeira',
                descricao: 'Fim anterior: '.$periodo->fim_previsto.'; novo: '.$dados['fim_previsto'].'; '.$dados['justificativa'],
                entidade: 'transicao_financeira', entidadeId: $transicao);
            return DB::table('transicoes_financeiras')->find($transicao);
        });
        return response()->json($registro);
    }

    public function iniciar(Request $request)
    {
        $dados = $request->validate([
            'inicio' => ['required', 'date'],
            'fim_previsto' => ['required', 'date', 'after_or_equal:inicio'],
            'motivo' => ['required', 'string', 'min:10', 'max:3000'],
            'responsaveis_admin_ids' => ['sometimes', 'array', 'min:1'],
            'responsaveis_admin_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);
        $registro = DB::transaction(function () use ($request, $dados) {
            if (DB::table('transicoes_financeiras')->where('status', 'ativa')->exists()
                || GestaoFinanceira::where('status', 'ativa')->exists()) {
                throw ValidationException::withMessages(['transicao' => 'Já existe período financeiro ativo.']);
            }
            if (GestaoFinanceira::where('inicio', '<=', $dados['fim_previsto'])
                ->where(fn ($q) => $q->whereNull('fim_previsto')->orWhere('fim_previsto', '>=', $dados['inicio']))->exists()
                || DB::table('transicoes_financeiras')->where('inicio', '<=', $dados['fim_previsto'])
                    ->where('fim_previsto', '>=', $dados['inicio'])->exists()) {
                throw ValidationException::withMessages(['transicao' => 'Período sobreposto a outra gestão ou transição.']);
            }
            $anterior = GestaoFinanceira::where('inicio', '<', $dados['inicio'])->orderByDesc('inicio')->first();
            if ($anterior && ($anterior->status !== 'encerrada' || $anterior->saldo_real_final_centavos === null)) {
                throw ValidationException::withMessages(['transicao' => 'Encerre e concilie a gestão anterior.']);
            }
            $responsaveis = $dados['responsaveis_admin_ids'] ?? [$request->user()->id];
            foreach ($responsaveis as $usuarioId) {
                if (! \App\Models\User::findOrFail($usuarioId)->isAdmin()) {
                    throw ValidationException::withMessages(['responsaveis_admin_ids' => 'Todos os responsáveis devem ser administradores.']);
                }
            }
            $id = DB::table('transicoes_financeiras')->insertGetId([
                'inicio' => $dados['inicio'], 'fim_previsto' => $dados['fim_previsto'],
                'motivo' => $dados['motivo'], 'status' => 'ativa',
                'saldo_inicial_centavos' => $anterior ? $anterior->saldo_real_final_centavos : 0,
                'responsavel_admin_id' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($responsaveis as $usuarioId) {
                DB::table('responsaveis_transicao_financeira')->insert([
                    'transicao_financeira_id' => $id, 'user_id' => $usuarioId,
                    'indicado_por' => $request->user()->id, 'indicado_em' => now(),
                ]);
            }
            Auditoria::registrar('iniciou_transicao_financeira', entidade: 'transicao_financeira', entidadeId: $id);
            return DB::table('transicoes_financeiras')->find($id);
        });
        return response()->json($registro, 201);
    }
}

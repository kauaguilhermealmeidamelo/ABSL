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

    public function iniciar(Request $request)
    {
        $dados = $request->validate([
            'inicio' => ['required', 'date'],
            'fim_previsto' => ['required', 'date', 'after_or_equal:inicio'],
            'motivo' => ['required', 'string', 'min:10', 'max:3000'],
        ]);
        $registro = DB::transaction(function () use ($request, $dados) {
            if (DB::table('transicoes_financeiras')->where('status', 'ativa')->exists()
                || GestaoFinanceira::where('status', 'ativa')->exists()) {
                throw ValidationException::withMessages(['transicao' => 'Já existe período financeiro ativo.']);
            }
            $anterior = GestaoFinanceira::where('inicio', '<', $dados['inicio'])->orderByDesc('inicio')->first();
            if ($anterior && ($anterior->status !== 'encerrada' || $anterior->saldo_real_final_centavos === null)) {
                throw ValidationException::withMessages(['transicao' => 'Encerre e concilie a gestão anterior.']);
            }
            $id = DB::table('transicoes_financeiras')->insertGetId([
                ...$dados, 'status' => 'ativa',
                'saldo_inicial_centavos' => $anterior ? $anterior->saldo_real_final_centavos : 0,
                'responsavel_admin_id' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            Auditoria::registrar('iniciou_transicao_financeira', entidade: 'transicao_financeira', entidadeId: $id);
            return DB::table('transicoes_financeiras')->find($id);
        });
        return response()->json($registro, 201);
    }
}

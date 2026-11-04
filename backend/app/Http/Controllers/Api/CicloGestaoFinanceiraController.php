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

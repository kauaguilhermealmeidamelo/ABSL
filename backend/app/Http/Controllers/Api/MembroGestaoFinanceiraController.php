<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MembroGestaoFinanceiraController extends Controller
{
    public function index(GestaoFinanceira $gestao)
    {
        return DB::table('membros_gestao_financeira')
            ->where('gestao_financeira_id', $gestao->id)->get();
    }

    public function desligar(Request $request, GestaoFinanceira $gestao, int $membro)
    {
        $dados = $request->validate(['justificativa' => ['required', 'string', 'min:10', 'max:2000']]);
        DB::transaction(function () use ($request, $gestao, $membro, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão encerrada é imutável.']);
            }
            $registro = DB::table('membros_gestao_financeira')
                ->where('gestao_financeira_id', $gestao->id)->where('id', $membro)->lockForUpdate()->first();
            abort_unless($registro, 404);
            if ($registro->desligado_em) {
                throw ValidationException::withMessages(['membro' => 'Membro já desligado.']);
            }
            DB::table('membros_gestao_financeira')->where('id', $membro)->update([
                'desligado_em' => now(), 'updated_at' => now(),
            ]);
            DB::table('vinculos_financeiros')->where('gestao_financeira_id', $gestao->id)
                ->where('user_id', $registro->user_id)->whereNull('fim_em')->update([
                    'fim_em' => now(), 'encerrado_por' => $request->user()->id,
                    'justificativa_encerramento' => $dados['justificativa'], 'updated_at' => now(),
                ]);
            Auditoria::registrar('desligou_membro_gestao', descricao: $dados['justificativa'],
                entidade: 'membro_gestao_financeira', entidadeId: $membro);
        });
        return response()->noContent();
    }

    public function registrar(Request $request, GestaoFinanceira $gestao)
    {
        $dados = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'cargo_institucional' => ['required', 'string', 'max:120'],
            'justificativa' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        $id = DB::transaction(function () use ($request, $gestao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão não admite alterações de membros.']);
            }
            if (DB::table('membros_gestao_financeira')->where('gestao_financeira_id', $gestao->id)
                ->where('user_id', $dados['user_id'])->exists()) {
                throw ValidationException::withMessages(['user_id' => 'Membro já cadastrado.']);
            }
            $id = DB::table('membros_gestao_financeira')->insertGetId([
                ...$dados, 'gestao_financeira_id' => $gestao->id,
                'registrado_por' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            Auditoria::registrar('registrou_membro_gestao', descricao: $dados['justificativa'], entidade: 'membro_gestao_financeira', entidadeId: $id);
            return $id;
        });
        return response()->json(['id' => $id], 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\User;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VinculoFinanceiroController extends Controller
{
    public function index(GestaoFinanceira $gestao)
    {
        return DB::table('vinculos_financeiros')
            ->where('gestao_financeira_id', $gestao->id)
            ->orderByDesc('inicio_em')
            ->get();
    }

    public function store(Request $request, GestaoFinanceira $gestao)
    {
        $dados = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'cargo' => ['required', Rule::in(['tesoureiro', 'tesoureiro_geral'])],
            'interino' => ['sometimes', 'boolean'],
            'fim_previsto_em' => ['nullable', 'date', 'after:now'],
            'justificativa' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        $interino = (bool) ($dados['interino'] ?? false);
        if ($interino && ($dados['cargo'] !== 'tesoureiro_geral' || empty($dados['fim_previsto_em']))) {
            throw ValidationException::withMessages(['interino' => 'Interinidade exige cargo de Tesoureiro-Geral e prazo final.']);
        }
        if (! $interino && $dados['cargo'] === 'tesoureiro_geral' && ! empty($dados['fim_previsto_em'])) {
            throw ValidationException::withMessages(['fim_previsto_em' => 'O prazo final é reservado à interinidade.']);
        }

        $id = DB::transaction(function () use ($request, $gestao, $dados, $interino) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão não admite nomeações.']);
            }
            User::findOrFail($dados['user_id']);
            $ativos = DB::table('vinculos_financeiros')
                ->where('gestao_financeira_id', $gestao->id)
                ->whereNull('fim_em')
                ->where('inicio_em', '<=', now())
                ->where(fn ($q) => $q->whereNull('fim_previsto_em')->orWhere('fim_previsto_em', '>', now()));

            if ($interino) {
                $vinculado = DB::table('membros_gestao_financeira')
                    ->where('gestao_financeira_id', $gestao->id)
                    ->where('user_id', $dados['user_id'])->whereNull('desligado_em')->exists();
                if (! $vinculado) {
                    throw ValidationException::withMessages(['user_id' => 'O interino deve integrar oficialmente esta gestão.']);
                }
                if ((clone $ativos)->where('cargo', 'tesoureiro_geral')->where('interino', true)->exists()) {
                    throw ValidationException::withMessages(['interino' => 'Já existe interino em exercício.']);
                }
                if (! (clone $ativos)->where('cargo', 'tesoureiro_geral')->where('interino', false)->exists()) {
                    throw ValidationException::withMessages(['interino' => 'Não existe titular a substituir temporariamente.']);
                }
            } elseif ($dados['cargo'] === 'tesoureiro_geral' && (clone $ativos)->where('cargo', 'tesoureiro_geral')->where('interino', false)->exists()) {
                throw ValidationException::withMessages(['cargo' => 'Já existe Tesoureiro-Geral titular. Encerre o vínculo anterior primeiro.']);
            }
            if (! $interino && (clone $ativos)->where('user_id', $dados['user_id'])->where('cargo', $dados['cargo'])->exists()) {
                throw ValidationException::withMessages(['user_id' => 'Usuário já possui esse cargo.']);
            }

            $id = DB::table('vinculos_financeiros')->insertGetId([
                'gestao_financeira_id' => $gestao->id,
                'user_id' => $dados['user_id'],
                'cargo' => $dados['cargo'],
                'interino' => $interino,
                'inicio_em' => now(),
                'fim_previsto_em' => $dados['fim_previsto_em'] ?? null,
                'justificativa' => $dados['justificativa'],
                'nomeado_por' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Auditoria::registrar('nomeou_responsavel_financeiro', descricao: $dados['justificativa'], entidade: 'vinculo_financeiro', entidadeId: $id);
            return $id;
        });

        return response()->json(['id' => $id], 201);
    }

    public function ajustarInterinidade(Request $request, GestaoFinanceira $gestao, int $vinculo)
    {
        $dados = $request->validate([
            'fim_previsto_em' => ['required', 'date', 'after:now'],
            'justificativa' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        DB::transaction(function () use ($request, $gestao, $vinculo, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão não admite ajustes.']);
            }
            $registro = DB::table('vinculos_financeiros')
                ->where('gestao_financeira_id', $gestao->id)
                ->where('id', $vinculo)->lockForUpdate()->first();
            abort_unless($registro, 404);
            if (! $registro->interino || $registro->fim_em !== null) {
                throw ValidationException::withMessages(['vinculo' => 'Somente interinidade não encerrada pode ser ajustada.']);
            }
            DB::table('vinculos_financeiros')->where('id', $vinculo)->update([
                'fim_previsto_em' => $dados['fim_previsto_em'], 'updated_at' => now(),
            ]);
            Auditoria::registrar('ajustou_interinidade_financeira',
                descricao: 'Prazo anterior: '.$registro->fim_previsto_em.'; novo prazo: '.$dados['fim_previsto_em'].'; '.$dados['justificativa'],
                entidade: 'vinculo_financeiro', entidadeId: $vinculo);
        });
        return response()->noContent();
    }

    public function encerrar(Request $request, GestaoFinanceira $gestao, int $vinculo)
    {
        $dados = $request->validate([
            'justificativa' => ['required', 'string', 'min:10', 'max:2000'],
        ]);
        DB::transaction(function () use ($request, $gestao, $vinculo, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if (! in_array($gestao->status, ['planejada', 'ativa'], true)) {
                throw ValidationException::withMessages(['gestao' => 'Gestão não admite substituições.']);
            }
            $registro = DB::table('vinculos_financeiros')
                ->where('gestao_financeira_id', $gestao->id)
                ->where('id', $vinculo)->lockForUpdate()->first();
            abort_unless($registro, 404);
            if ($registro->fim_em !== null) {
                throw ValidationException::withMessages(['vinculo' => 'Vínculo já encerrado.']);
            }
            DB::table('vinculos_financeiros')->where('id', $vinculo)->update([
                'fim_em' => now(),
                'encerrado_por' => $request->user()->id,
                'justificativa_encerramento' => $dados['justificativa'],
                'updated_at' => now(),
            ]);
            Auditoria::registrar('encerrou_vinculo_financeiro', descricao: $dados['justificativa'], entidade: 'vinculo_financeiro', entidadeId: $vinculo);
        });
        return response()->noContent();
    }
}

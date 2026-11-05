<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Services\AutorizacaoFinanceira;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeclaracaoInatividadeController extends Controller
{
    private function validarInatividade(GestaoFinanceira $gestao): void
    {
        if ($gestao->status !== 'ativa' || $gestao->movimentacoes()->where('status', 'confirmada')->exists()) {
            throw ValidationException::withMessages(['gestao' => 'A declaração exige gestão ativa sem movimentações confirmadas.']);
        }
        if ($gestao->movimentacoes()->where('status', 'pendente')->exists()) {
            throw ValidationException::withMessages(['gestao' => 'Resolva as movimentações pendentes.']);
        }
    }

    public function salvar(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao)
    {
        abort_unless($autorizacao->podeGerenciarFechamento($request->user(), $gestao), 403);
        $dados = $request->validate(['observacoes' => ['nullable', 'string', 'max:5000']]);

        $declaracao = DB::transaction(function () use ($request, $gestao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            $this->validarInatividade($gestao);
            $anterior = DB::table('declaracoes_inatividade_financeira')
                ->where('gestao_financeira_id', $gestao->id)->lockForUpdate()->first();

            if ($anterior && $anterior->status === 'publicada') {
                throw ValidationException::withMessages(['declaracao' => 'Declaração publicada é imutável.']);
            }

            $valores = [
                'observacoes' => $dados['observacoes'] ?? null,
                'status' => 'rascunho',
                'aprovada_tesouraria_por' => null,
                'cargo_aprovador_tesouraria' => null,
                'aprovada_tesouraria_em' => null,
                'aprovada_admin_por' => null,
                'aprovada_admin_em' => null,
                'updated_at' => now(),
            ];
            if ($anterior) {
                DB::table('declaracoes_inatividade_financeira')->where('id', $anterior->id)
                    ->update([...$valores, 'versao' => $anterior->versao + 1]);
                $id = $anterior->id;
            } else {
                $id = DB::table('declaracoes_inatividade_financeira')->insertGetId([
                    ...$valores, 'gestao_financeira_id' => $gestao->id,
                    'criada_por' => $request->user()->id, 'versao' => 1, 'created_at' => now(),
                ]);
            }
            Auditoria::registrar('salvou_declaracao_inatividade', entidade: 'declaracao_inatividade_financeira', entidadeId: $id);
            return DB::table('declaracoes_inatividade_financeira')->find($id);
        });

        return response()->json($declaracao);
    }

    public function aprovar(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao)
    {
        $dados = $request->validate(['papel' => ['required', 'in:tesouraria,administracao']]);
        $declaracao = DB::transaction(function () use ($request, $gestao, $autorizacao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            $this->validarInatividade($gestao);
            $registro = DB::table('declaracoes_inatividade_financeira')
                ->where('gestao_financeira_id', $gestao->id)->lockForUpdate()->first();
            if (! $registro || $registro->status === 'publicada') {
                throw ValidationException::withMessages(['declaracao' => 'Declaração inexistente ou já publicada.']);
            }

            $usuario = $request->user();
            if ($dados['papel'] === 'administracao') {
                abort_unless($usuario->isAdmin(), 403);
                if ((int) $registro->aprovada_tesouraria_por === $usuario->id) {
                    throw ValidationException::withMessages(['aprovacao' => 'As aprovações exigem duas pessoas distintas.']);
                }
                $campos = ['aprovada_admin_por' => $usuario->id, 'aprovada_admin_em' => now()];
            } else {
                abort_unless($autorizacao->podeGerenciarFechamento($usuario, $gestao), 403);
                // Administradores só podem aprovar como tesouraria se possuírem vínculo válido de titular ou interino.
                $vinculo = DB::table('vinculos_financeiros')
                    ->where('gestao_financeira_id', $gestao->id)->where('user_id', $usuario->id)
                    ->where('cargo', 'tesoureiro_geral')->whereNull('fim_em')
                    ->where('inicio_em', '<=', now())
                    ->where(fn ($q) => $q->whereNull('fim_previsto_em')->orWhere('fim_previsto_em', '>', now()))
                    ->orderByDesc('interino')->first();
                abort_unless($vinculo, 403);
                if (! $vinculo->interino && DB::table('vinculos_financeiros')
                    ->where('gestao_financeira_id', $gestao->id)
                    ->where('cargo', 'tesoureiro_geral')->where('interino', true)
                    ->whereNull('fim_em')->where('inicio_em', '<=', now())
                    ->where(fn ($q) => $q->whereNull('fim_previsto_em')->orWhere('fim_previsto_em', '>', now()))
                    ->exists()) {
                    abort(403, 'O titular não aprova durante interinidade ativa.');
                }
                if ((int) $registro->aprovada_admin_por === $usuario->id) {
                    throw ValidationException::withMessages(['aprovacao' => 'As aprovações exigem duas pessoas distintas.']);
                }
                $campos = [
                    'aprovada_tesouraria_por' => $usuario->id,
                    'cargo_aprovador_tesouraria' => $vinculo->interino ? 'Tesoureiro-Geral Interino' : 'Tesoureiro-Geral',
                    'aprovada_tesouraria_em' => now(),
                ];
            }
            DB::table('declaracoes_inatividade_financeira')->where('id', $registro->id)
                ->update([...$campos, 'updated_at' => now()]);
            Auditoria::registrar('aprovou_declaracao_inatividade', descricao: $dados['papel'], entidade: 'declaracao_inatividade_financeira', entidadeId: $registro->id);
            return DB::table('declaracoes_inatividade_financeira')->find($registro->id);
        });
        return response()->json($declaracao);
    }

    public function publicar(Request $request, GestaoFinanceira $gestao)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $declaracao = DB::transaction(function () use ($gestao) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            $this->validarInatividade($gestao);
            if ($gestao->saldo_real_final_centavos === null || $gestao->conciliado_em === null) {
                throw ValidationException::withMessages(['gestao' => 'Concilie o saldo real antes de publicar a declaração.']);
            }
            if ((int) $gestao->saldo_real_final_centavos - (int) $gestao->saldo_inicial_centavos
                !== (int) $gestao->diferenca_conciliacao_centavos) {
                throw ValidationException::withMessages(['gestao' => 'A conciliação não corresponde ao saldo contábil.']);
            }
            if (! $gestao->fim_previsto || $gestao->fim_previsto->isFuture()) {
                throw ValidationException::withMessages(['gestao' => 'Aguarde o término da gestão antes de publicar a declaração definitiva.']);
            }
            for ($mes = $gestao->inicio->copy()->startOfMonth();
                $mes->lessThanOrEqualTo($gestao->fim_previsto->copy()->startOfMonth());
                $mes->addMonth()) {
                if (! \App\Models\RelatorioFinanceiro::where('gestao_financeira_id', $gestao->id)
                    ->where('ano', $mes->year)->where('mes', $mes->month)
                    ->where('status', 'publicado')->exists()) {
                    throw ValidationException::withMessages(['gestao' => 'Publique todos os relatórios mensais antes da declaração definitiva.']);
                }
            }
            $registro = DB::table('declaracoes_inatividade_financeira')
                ->where('gestao_financeira_id', $gestao->id)->lockForUpdate()->first();
            if (! $registro || $registro->status !== 'rascunho'
                || ! $registro->aprovada_admin_por || ! $registro->aprovada_tesouraria_por
                || $registro->aprovada_admin_por === $registro->aprovada_tesouraria_por) {
                throw ValidationException::withMessages(['declaracao' => 'Exige aprovação de tesouraria e administração por pessoas distintas.']);
            }
            $ultimaAlteracao = $gestao->movimentacoes()->max('updated_at');
            if ($ultimaAlteracao && $gestao->conciliado_em->lt($ultimaAlteracao)) {
                throw ValidationException::withMessages(['gestao' => 'Refaça a conciliação após a última alteração financeira.']);
            }
            if ($gestao->conciliado_em->greaterThan(\Illuminate\Support\Carbon::parse($registro->aprovada_admin_em))
                || $gestao->conciliado_em->greaterThan(\Illuminate\Support\Carbon::parse($registro->aprovada_tesouraria_em))) {
                throw ValidationException::withMessages(['declaracao' => 'A conciliação mudou após uma aprovação. Atualize o rascunho e obtenha duas novas aprovações.']);
            }
            DB::table('declaracoes_inatividade_financeira')->where('id', $registro->id)
                ->update(['status' => 'publicada', 'publicada_em' => now(), 'updated_at' => now()]);
            $pdf = app(PdfInatividadeController::class)->download(
                $gestao, app(\App\Services\PdfFinanceiroSimples::class)
            )->getContent();
            DB::table('declaracoes_inatividade_financeira')->where('id', $registro->id)
                ->update(['pdf_base64' => base64_encode($pdf)]);
            Auditoria::registrar('publicou_declaracao_inatividade', entidade: 'declaracao_inatividade_financeira', entidadeId: $registro->id);
            $publicada = DB::table('declaracoes_inatividade_financeira')->find($registro->id);
            unset($publicada->pdf_base64);
            return $publicada;
        });
        return response()->json($declaracao);
    }

    public function rascunho(Request $request, GestaoFinanceira $gestao, AutorizacaoFinanceira $autorizacao)
    {
        abort_unless($autorizacao->podeGerenciarFechamento($request->user(), $gestao), 403);
        $registro = DB::table('declaracoes_inatividade_financeira')
            ->where('gestao_financeira_id', $gestao->id)->first();
        if ($registro) {
            unset($registro->pdf_base64);
        }
        return response()->json($registro);
    }

    public function consultar(GestaoFinanceira $gestao)
    {
        $registro = DB::table('declaracoes_inatividade_financeira')
            ->where('gestao_financeira_id', $gestao->id)->where('status', 'publicada')->first();
        abort_unless($registro, 404);
        unset($registro->pdf_base64);
        return response()->json([
            'gestao' => $gestao->only('id', 'nome', 'inicio', 'fim_previsto', 'saldo_inicial_centavos', 'saldo_real_final_centavos', 'diferenca_conciliacao_centavos'),
            'declaracao' => $registro,
        ]);
    }
}

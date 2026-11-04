<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\RelatorioFinanceiro;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EsclarecimentoFinanceiroController extends Controller
{
    public function index(GestaoFinanceira $gestao)
    {
        return DB::table('esclarecimentos_financeiros')
            ->where('gestao_financeira_id', $gestao->id)
            ->orderBy('publicado_em')->get();
    }

    public function publicar(Request $request, GestaoFinanceira $gestao)
    {
        $dados = $request->validate([
            'texto' => ['required', 'string', 'min:10', 'max:10000'],
            'relatorio_financeiro_id' => ['nullable', 'integer', 'exists:relatorios_financeiros,id'],
        ]);
        $nota = DB::transaction(function () use ($request, $gestao, $dados) {
            $gestao = GestaoFinanceira::whereKey($gestao->id)->lockForUpdate()->firstOrFail();
            if ($gestao->status !== 'encerrada') {
                throw ValidationException::withMessages(['gestao' => 'Notas de esclarecimento são exclusivas de gestões encerradas.']);
            }
            if (! empty($dados['relatorio_financeiro_id'])) {
                $relatorio = RelatorioFinanceiro::findOrFail($dados['relatorio_financeiro_id']);
                if ($relatorio->gestao_financeira_id !== $gestao->id || $relatorio->status !== 'publicado') {
                    throw ValidationException::withMessages(['relatorio_financeiro_id' => 'Relatório não pertence à gestão encerrada.']);
                }
            }
            $id = DB::table('esclarecimentos_financeiros')->insertGetId([
                'gestao_financeira_id' => $gestao->id,
                'relatorio_financeiro_id' => $dados['relatorio_financeiro_id'] ?? null,
                'texto' => $dados['texto'],
                'publicado_por' => $request->user()->id,
                'publicado_em' => now(),
            ]);
            Auditoria::registrar('publicou_esclarecimento_financeiro', entidade: 'esclarecimento_financeiro', entidadeId: $id);
            return DB::table('esclarecimentos_financeiros')->find($id);
        });
        return response()->json($nota, 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GestaoFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Support\Auditoria;
use App\Services\AutorizacaoFinanceira;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ComprovanteFinanceiroController extends Controller
{
    public function store(Request $request, MovimentacaoFinanceira $movimentacao, AutorizacaoFinanceira $autorizacao)
    {
        $request->validate(['arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']]);
        $gestao = GestaoFinanceira::whereKey($movimentacao->gestao_financeira_id)->firstOrFail();
        abort_unless($autorizacao->podeOperar($request->user(), $gestao), 403);
        if ($gestao->status !== 'ativa') {
            throw ValidationException::withMessages(['gestao' => 'Não é possível anexar arquivos a uma gestão encerrada.']);
        }
        $arquivo = $request->file('arquivo');
        $caminho = $arquivo->store('financeiro/comprovantes', 'local');
        try {
            $registro = DB::table('comprovantes_financeiros')->insertGetId([
                'movimentacao_financeira_id' => $movimentacao->id,
                'arquivo_privado' => $caminho,
                'nome_original' => basename($arquivo->getClientOriginalName()),
                'enviado_por' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($caminho);
            throw $e;
        }
        Auditoria::registrar('anexou_comprovante_financeiro', entidade: 'comprovante_financeiro', entidadeId: $registro);
        return response()->json(['id' => $registro, 'nome_original' => basename($arquivo->getClientOriginalName())], 201);
    }

    public function index(Request $request, MovimentacaoFinanceira $movimentacao, AutorizacaoFinanceira $autorizacao)
    {
        $gestao = GestaoFinanceira::findOrFail($movimentacao->gestao_financeira_id);
        abort_unless($autorizacao->podeConsultarHistorico($request->user(), $gestao), 403);
        return DB::table('comprovantes_financeiros')->where('movimentacao_financeira_id', $movimentacao->id)
            ->get(['id', 'nome_original', 'created_at']);
    }

    public function download(Request $request, int $id, AutorizacaoFinanceira $autorizacao)
    {
        $registro = DB::table('comprovantes_financeiros')->find($id);
        abort_unless($registro, 404);
        $movimentacao = MovimentacaoFinanceira::findOrFail($registro->movimentacao_financeira_id);
        $gestao = GestaoFinanceira::findOrFail($movimentacao->gestao_financeira_id);
        abort_unless($autorizacao->podeConsultarHistorico($request->user(), $gestao), 403);
        abort_unless(Storage::disk('local')->exists($registro->arquivo_privado), 404);
        return Storage::disk('local')->download($registro->arquivo_privado, $registro->nome_original);
    }
}

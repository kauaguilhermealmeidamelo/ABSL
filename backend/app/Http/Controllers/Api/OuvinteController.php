<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ouvinte;
use App\Support\Auditoria;
use Illuminate\Http\Request;

class OuvinteController extends Controller
{
    public function index()
    {
        return Ouvinte::orderBy('created_at', 'desc')->limit(200)->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'turma' => 'nullable|string|max:10',
            'mensagem' => 'required|string',
            'tipo' => 'nullable|in:sugestao,reclamacao,elogio,duvida',
            'anonimo' => 'boolean',
        ]);

        if ($data['anonimo'] ?? false) {
            $data['nome'] = null;
            $data['email'] = null;
        }

        $data['status'] = 'pendente';

        return response()->json(Ouvinte::create($data), 201);
    }

    public function show(string $id)
    {
        return Ouvinte::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $ouvinte = Ouvinte::findOrFail($id);

        $data = $request->validate([
            'status' => 'sometimes|required|in:pendente,respondido,fechado',
            'resposta' => 'nullable|string',
        ]);

        $respondeuAgora = array_key_exists('resposta', $data) && $data['resposta'];

        if ($respondeuAgora) {
            $data['data_resposta'] = now();
            $data['respondido_por'] = $request->user()->id;
            $data['status'] = $data['status'] ?? 'respondido';
        }

        if (($data['status'] ?? null) === 'fechado') {
            $data['data_fechamento'] = now();
        }

        $ouvinte->update($data);

        if ($respondeuAgora) {
            Auditoria::registrar(
                'respondeu_ouvidoria',
                descricao: "Respondeu a mensagem #{$ouvinte->id}",
                entidade: 'ouvinte',
                entidadeId: $ouvinte->id
            );
        } elseif (isset($data['status'])) {
            Auditoria::registrar(
                'atualizou_status_ouvidoria',
                descricao: "Alterou status da mensagem #{$ouvinte->id} para \"{$data['status']}\"",
                entidade: 'ouvinte',
                entidadeId: $ouvinte->id
            );
        }

        return $ouvinte;
    }

    public function destroy(string $id)
    {
        $ouvinte = Ouvinte::findOrFail($id);
        $ouvinte->delete();

        Auditoria::registrar(
            'excluiu_ouvidoria',
            descricao: "Excluiu a mensagem #{$id}",
            entidade: 'ouvinte',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }

    public function consultarProtocolo(string $id)
    {
        $ouvinte = Ouvinte::findOrFail($id);

        return response()->json([
            'id' => $ouvinte->id,
            'texto' => $ouvinte->mensagem,
            'status' => $ouvinte->status,
            'resposta' => $ouvinte->resposta,
            'data_resposta' => $ouvinte->data_resposta,
            'data_envio' => $ouvinte->created_at,
        ]);
    }

    public function respondidas()
    {
        $mensagens = Ouvinte::where('status', 'respondido')
            ->whereNotNull('resposta')
            ->orderBy('data_resposta', 'desc')
            ->limit(100)
            ->get(['id', 'mensagem', 'turma', 'tipo', 'resposta', 'data_resposta', 'created_at']);

        return $mensagens->map(fn ($m) => [
            'id' => $m->id,
            'texto' => $m->mensagem,
            'turma' => $m->turma,
            'tipo' => $m->tipo,
            'resposta' => $m->resposta,
            'data_resposta' => $m->data_resposta,
            'data_envio' => $m->created_at,
        ]);
    }
}
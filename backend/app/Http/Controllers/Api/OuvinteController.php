<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ouvinte;
use Illuminate\Http\Request;

class OuvinteController extends Controller
{
    /**
     * Lista todas as mensagens (visão do admin). Protegido por 'auth:sanctum'.
     */
    public function index()
    {
        return Ouvinte::orderBy('created_at', 'desc')->limit(200)->get();
    }

    /**
     * Envia uma nova mensagem (rota pública — formulário da Ouvidoria).
     */
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

    /**
     * Exibe uma mensagem específica.
     */
    public function show(string $id)
    {
        return Ouvinte::findOrFail($id);
    }

    /**
     * Atualiza uma mensagem (ex: registrar resposta do grêmio).
     * Protegido por 'auth:sanctum'.
     */
    public function update(Request $request, string $id)
    {
        $ouvinte = Ouvinte::findOrFail($id);

        $data = $request->validate([
            'status' => 'sometimes|required|in:pendente,respondido,fechado',
            'resposta' => 'nullable|string',
        ]);

        if (array_key_exists('resposta', $data) && $data['resposta']) {
            $data['data_resposta'] = now();
            $data['respondido_por'] = $request->user()->id;
            $data['status'] = $data['status'] ?? 'respondido';
        }

        if (($data['status'] ?? null) === 'fechado') {
            $data['data_fechamento'] = now();
        }

        $ouvinte->update($data);

        return $ouvinte;
    }

    /**
     * Remove uma mensagem. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Ouvinte::findOrFail($id)->delete();

        return response()->noContent();
    }

    /**
     * Consulta pública por protocolo (o próprio id da mensagem). Usada pelo
     * usuário comum para acompanhar a resposta da mensagem que ele mesmo
     * enviou, sem precisar de login admin.
     */
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

    /**
     * Lista pública das mensagens já respondidas, para servir como um
     * "central de suporte" — qualquer visitante pode ver perguntas e
     * respostas anteriores, sem precisar ter enviado nada. Nome e e-mail
     * NUNCA são retornados aqui, mesmo quando a mensagem original não era
     * anônima, porque a listagem é pública.
     */
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
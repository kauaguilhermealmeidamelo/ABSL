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
        // Sem paginação real ainda (mudaria o formato da resposta e quebraria
        // o front hoje, que espera um array puro). Como stopgap, ordena e
        // limita — ver README para o plano de paginação completa.
        return Ouvinte::orderBy('created_at', 'desc')->limit(200)->get();
    }

    /**
     * Envia uma nova mensagem (rota pública — formulário da Ouvidoria).
     * Quando 'anonimo' é true, nome/email não devem ser enviados pelo
     * frontend mesmo que os campos aceitem null.
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
     *
     * Atenção: esta rota é pública em routes/api.php. Como mensagens não
     * anônimas guardam nome/email, expor '/ouvintes/{id}' publicamente
     * permite que qualquer pessoa com o id leia dados pessoais de quem
     * enviou. Considerar mover esta rota para o grupo 'auth:sanctum'
     * (mantendo apenas 'store' pública) caso isso não seja intencional.
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
     * Remove uma mensagem. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Ouvinte::findOrFail($id)->delete();

        return response()->noContent();
    }
}

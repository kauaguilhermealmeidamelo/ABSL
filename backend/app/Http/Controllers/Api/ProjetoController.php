<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    /**
     * Lista projetos, mais recentes primeiro.
     */
    public function index()
    {
        return Projeto::orderBy('created_at', 'desc')->get();
    }

    /**
     * Exibe um projeto específico.
     */
    public function show(string $id)
    {
        return Projeto::findOrFail($id);
    }

    /**
     * Cria um novo projeto. Protegido por 'auth:sanctum' em routes/api.php.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'diretoria' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'imagem_url' => 'nullable|string',
            'conteudo_detalhado' => 'nullable|string',
            // Valores reais usados pela UI (ver ProjetoFormModal.vue),
            // diferentes do default 'ativo' da migration original.
            'status' => 'nullable|in:em_andamento,concluido',
            'data_conclusao' => 'nullable|string|max:100',
            'destaque' => 'boolean',
        ]);

        $data['responsavel_id'] = $request->user()->id;

        return response()->json(Projeto::create($data), 201);
    }

    /**
     * Atualiza um projeto existente. Protegido por 'auth:sanctum'.
     */
    public function update(Request $request, string $id)
    {
        $projeto = Projeto::findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'diretoria' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'imagem_url' => 'nullable|string',
            'conteudo_detalhado' => 'nullable|string',
            'status' => 'nullable|in:em_andamento,concluido',
            'data_conclusao' => 'nullable|string|max:100',
            'destaque' => 'boolean',
        ]);

        $projeto->update($data);

        return $projeto;
    }

    /**
     * Remove um projeto. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Projeto::findOrFail($id)->delete();

        return response()->noContent();
    }
}
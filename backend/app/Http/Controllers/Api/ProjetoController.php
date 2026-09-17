<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjetoResource;
use App\Models\Projeto;
use App\Support\Auditoria;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    public function index()
    {
        return ProjetoResource::collection(
            Projeto::orderBy('created_at', 'desc')->get()
        );
    }

    public function show(string $id)
    {
        return new ProjetoResource(Projeto::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'diretoria' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'imagem_url' => 'nullable|string',
            'conteudo_detalhado' => 'nullable|string',
            'status' => 'nullable|in:em_andamento,concluido',
            'data_conclusao' => 'nullable|string|max:100',
            'destaque' => 'boolean',
        ]);

        $data['responsavel_id'] = $request->user()->id;

        $projeto = Projeto::create($data);

        Auditoria::registrar(
            'criou_projeto',
            descricao: "Criou o projeto \"{$projeto->nome}\"",
            entidade: 'projeto',
            entidadeId: $projeto->id
        );

        return new ProjetoResource($projeto);
    }

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

        Auditoria::registrar(
            'editou_projeto',
            descricao: "Editou o projeto \"{$projeto->nome}\"",
            entidade: 'projeto',
            entidadeId: $projeto->id
        );

        return new ProjetoResource($projeto);
    }

    public function destroy(string $id)
    {
        $projeto = Projeto::findOrFail($id);
        $nome = $projeto->nome;
        $projeto->delete();

        Auditoria::registrar(
            'excluiu_projeto',
            descricao: "Excluiu o projeto \"{$nome}\"",
            entidade: 'projeto',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index()
    {
        return NoticiaResource::collection(
            Noticia::where('ativo', true)->orderBy('data_publicacao', 'desc')->get()
        );
    }

    public function show(string $id)
    {
        return new NoticiaResource(Noticia::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:gremio,escola',
            'descricao' => 'required|string',
            'conteudo' => 'nullable|string',
            'imagem_url' => 'nullable|string',
            'data_publicacao' => 'nullable|date',
            'destaque' => 'boolean',
            'ativo' => 'boolean',
        ]);

        $data['autor_id'] = $request->user()->id;

        return new NoticiaResource(Noticia::create($data));
    }

    public function update(Request $request, string $id)
    {
        $noticia = Noticia::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'categoria' => 'sometimes|required|in:gremio,escola',
            'descricao' => 'sometimes|required|string',
            'conteudo' => 'nullable|string',
            'imagem_url' => 'nullable|string',
            'data_publicacao' => 'nullable|date',
            'destaque' => 'boolean',
            'ativo' => 'boolean',
        ]);

        $noticia->update($data);

        return new NoticiaResource($noticia);
    }

    public function destroy(string $id)
    {
        Noticia::findOrFail($id)->delete();

        return response()->noContent();
    }
}
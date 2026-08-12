<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    /**
     * Lista notícias ativas, mais recentes primeiro.
     */
    public function index()
    {
        return Noticia::where('ativo', true)
            ->orderBy('data_publicacao', 'desc')
            ->get();
    }

    /**
     * Exibe uma notícia específica.
     */
    public function show(string $id)
    {
        return Noticia::findOrFail($id);
    }

    /**
     * Cria uma nova notícia. Protegido por 'auth:sanctum' em routes/api.php.
     */
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

        return response()->json(Noticia::create($data), 201);
    }

    /**
     * Atualiza uma notícia existente. Protegido por 'auth:sanctum'.
     */
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

        return $noticia;
    }

    /**
     * Remove uma notícia. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Noticia::findOrFail($id)->delete();

        return response()->noContent();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaComentarioResource;
use App\Models\Noticia;
use App\Models\NoticiaComentario;
use App\Support\Auditoria;
use Illuminate\Http\Request;

class NoticiaComentarioController extends Controller
{
    public function index(string $id)
    {
        $comentarios = NoticiaComentario::where('noticia_id', $id)
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->limit(200)
            ->get();

        return NoticiaComentarioResource::collection($comentarios);
    }

    public function store(Request $request, string $id)
    {
        $noticia = Noticia::findOrFail($id);

        $data = $request->validate(['texto' => 'required|string|max:500']);

        $comentario = NoticiaComentario::create([
            'noticia_id' => $noticia->id,
            'user_id' => $request->user()->id,
            'texto' => $data['texto'],
        ]);

        $comentario->load('user:id,name');

        return new NoticiaComentarioResource($comentario);
    }

    // Restrita a 'staff' nas rotas — dado que os comentaristas são alunos
    // (menores de idade em boa parte dos casos), a equipe do grêmio precisa
    // conseguir remover qualquer comentário problemático sem depender do
    // autor apagar por conta própria.
    public function destroy(string $id, string $comentarioId)
    {
        $comentario = NoticiaComentario::where('noticia_id', $id)->findOrFail($comentarioId);
        $comentario->delete();

        Auditoria::registrar(
            'excluiu_comentario_noticia',
            descricao: "Excluiu um comentário da notícia #{$id}",
            entidade: 'noticia_comentario',
            entidadeId: (int) $comentarioId
        );

        return response()->noContent();
    }
}
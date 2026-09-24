<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaComentarioResource;
use App\Models\Noticia;
use App\Models\NoticiaComentario;
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
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NoticiaController extends Controller
{
    public function index()
    {
        return Noticia::where('ativo', true)
            ->orderBy('data_publicacao', 'desc')
            ->get();
    }

    public function show(string $id)
    {
        return Noticia::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'required|string',
            'conteudo'         => 'nullable|string',
            'imagem_url'       => 'nullable|string',
            'destaque'         => 'boolean',
        ]);

        $data['autor_id'] = $request->user()->id;

        return Noticia::create($data);
    }

    public function update(Request $request, string $id)
    {
        $this->ensureAdmin($request);

        $noticia = Noticia::findOrFail($id);

        $data = $request->validate([
            'titulo'     => 'sometimes|required|string|max:255',
            'descricao'  => 'sometimes|required|string',
            'conteudo'   => 'nullable|string',
            'imagem_url' => 'nullable|string',
            'destaque'   => 'boolean',
            'ativo'      => 'boolean',
        ]);

        $noticia->update($data);

        return $noticia;
    }

    public function destroy(Request $request, string $id)
    {
        $this->ensureAdmin($request);

        Noticia::findOrFail($id)->delete();

        return response()->noContent();
    }

    private function ensureAdmin(Request $request): void
    {
        if (! $request->user() || ! $request->user()->is_admin) {
            throw new HttpException(403, 'Acesso restrito a administradores.');
        }
    }
}
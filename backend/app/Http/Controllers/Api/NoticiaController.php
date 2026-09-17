<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class NoticiaController extends Controller
{
    public function index()
    {
        $rows = Cache::remember('noticias.index', 300, function () {
            return Noticia::where('ativo', true)->orderBy('data_publicacao', 'desc')->get()->toArray();
        });

        return NoticiaResource::collection(Noticia::hydrate($rows));
    }

    public function show(string $id)
    {
        return new NoticiaResource(Noticia::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:gremio,escola',
            'descricao' => 'required|string',
            'conteudo' => 'nullable|string',
            'imagem_url' => 'nullable|string',
            'imagem' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/gif|max:5120',
            'data_publicacao' => 'required|date',
            'destaque' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
        ]);

        $data = Arr::except($validated, ['imagem']);

        if ($request->hasFile('imagem')) {
            $data['imagem_url'] = $this->storeImagem($request->file('imagem'));
        }

        $data['ativo'] = $data['ativo'] ?? true;
        $data['destaque'] = $data['destaque'] ?? false;

        Cache::forget('noticias.index');
        $data['autor_id'] = $request->user()->id;

        $noticia = Noticia::create($data);

        Auditoria::registrar(
            'criou_noticia',
            descricao: "Criou a notícia \"{$noticia->titulo}\"",
            entidade: 'noticia',
            entidadeId: $noticia->id
        );

        return new NoticiaResource($noticia);
    }

    public function update(Request $request, string $id)
    {
        $noticia = Noticia::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'categoria' => 'sometimes|required|in:gremio,escola',
            'descricao' => 'sometimes|required|string',
            'conteudo' => 'nullable|string',
            'imagem_url' => 'nullable|string',
            'imagem' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/gif|max:5120',
            'data_publicacao' => 'nullable|date',
            'destaque' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
        ]);

        $data = Arr::except($validated, ['imagem']);

        if ($request->hasFile('imagem')) {
            $this->deleteImagemAntiga($noticia->imagem_url);
            $data['imagem_url'] = $this->storeImagem($request->file('imagem'));
        }

        $noticia->update($data);
        Cache::forget('noticias.index');

        Auditoria::registrar(
            'editou_noticia',
            descricao: "Editou a notícia \"{$noticia->titulo}\"",
            entidade: 'noticia',
            entidadeId: $noticia->id
        );

        return new NoticiaResource($noticia);
    }

    public function destroy(string $id)
    {
        $noticia = Noticia::findOrFail($id);
        $titulo = $noticia->titulo;
        $this->deleteImagemAntiga($noticia->imagem_url);
        $noticia->delete();
        Cache::forget('noticias.index');

        Auditoria::registrar(
            'excluiu_noticia',
            descricao: "Excluiu a notícia \"{$titulo}\"",
            entidade: 'noticia',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }

    private function storeImagem(\Illuminate\Http\UploadedFile $file): string
    {
        $path = $file->store('noticias', 'public');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($path);
    }

    private function deleteImagemAntiga(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = preg_replace('#^.*/storage/#', '', $url);

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
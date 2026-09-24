<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use App\Models\NoticiaCurtida;
use App\Models\NoticiaMidia;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $rows = Cache::remember('noticias.index', 300, function () {
            return Noticia::where('ativo', true)
                ->orderBy('data_publicacao', 'desc')
                ->withCount(['curtidas as curtidas_count', 'comentarios as comentarios_count'])
                ->get()
                ->toArray();
        });

        $noticias = Noticia::hydrate($rows);
        $ids = $noticias->pluck('id');

        // 'curtido' (por usuário) e 'midias' (relação aninhada) não entram
        // no blob cacheado: o primeiro varia por pessoa, o segundo não
        // sobrevive ao ciclo toArray()/hydrate() — vira atributo solto,
        // não relação carregada. Buscamos os dois à parte e religamos ao
        // Model já hidratado.
        $curtidasDoUsuario = collect();
        if ($userId = $request->user()?->id) {
            $curtidasDoUsuario = NoticiaCurtida::where('user_id', $userId)
                ->whereIn('noticia_id', $ids)
                ->pluck('noticia_id')
                ->flip();
        }

        $midiasPorNoticia = NoticiaMidia::whereIn('noticia_id', $ids)
            ->orderBy('ordem')
            ->get()
            ->groupBy('noticia_id');

        $noticias->each(function (Noticia $n) use ($curtidasDoUsuario, $midiasPorNoticia) {
            $n->curtido = $curtidasDoUsuario->has($n->id);
            $n->setRelation('midias', $midiasPorNoticia->get($n->id, collect()));
        });

        return NoticiaResource::collection($noticias);
    }

    public function show(Request $request, string $id)
    {
        $userId = $request->user()?->id;

        $noticia = Noticia::with('midias')
            ->withCount(['curtidas as curtidas_count', 'comentarios as comentarios_count'])
            ->withExists(['curtidas as curtido' => fn ($q) => $q->where('user_id', $userId ?? 0)])
            ->findOrFail($id);

        return new NoticiaResource($noticia);
    }

    public function store(Request $request)
    {
        // Não recebe mais upload de imagem aqui — fotos/vídeos são
        // adicionados via NoticiaMidiaController, depois que a notícia já
        // tem um id. Isso é JSON puro, sem multipart, o que também evita
        // o problema de boolean virando string "true" no FormData.
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:gremio,escola',
            'descricao' => 'required|string',
            'conteudo' => 'nullable|string',
            'data_publicacao' => 'required|date',
            'destaque' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
        ]);

        $validated['ativo'] = $validated['ativo'] ?? true;
        $validated['destaque'] = $validated['destaque'] ?? false;
        $validated['autor_id'] = $request->user()->id;

        $noticia = Noticia::create($validated);
        Cache::forget('noticias.index');

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
            'data_publicacao' => 'nullable|date',
            'destaque' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
        ]);

        $noticia->update($validated);
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
        $noticia = Noticia::with('midias')->findOrFail($id);
        $titulo = $noticia->titulo;

        foreach ($noticia->midias as $midia) {
            $this->deleteArquivo($midia->url);
        }
        $this->deleteArquivo($noticia->imagem_url); // legado

        $noticia->delete(); // cascadeOnDelete cuida das linhas de noticia_midias
        Cache::forget('noticias.index');

        Auditoria::registrar(
            'excluiu_noticia',
            descricao: "Excluiu a notícia \"{$titulo}\"",
            entidade: 'noticia',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }

    private function deleteArquivo(?string $url): void
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
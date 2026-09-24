<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticiaResource;
use App\Models\Noticia;
use App\Models\NoticiaMidia;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class NoticiaMidiaController extends Controller
{
    private const MIMES = 'jpeg,png,webp,gif,mp4,mov,webm,ogg';

    public function store(Request $request, string $id)
    {
        $noticia = Noticia::findOrFail($id);

        $request->validate([
            'midias' => 'required|array|min:1|max:10',
            'midias.*' => 'file|mimes:' . self::MIMES . '|max:51200', // 50MB
        ]);

        $ordem = (int) $noticia->midias()->max('ordem');

        foreach ($request->file('midias') as $arquivo) {
            $ordem++;
            $tipo = str_starts_with((string) $arquivo->getMimeType(), 'video/') ? 'video' : 'imagem';
            $path = $arquivo->store('noticias/midias', 'public');

            NoticiaMidia::create([
                'noticia_id' => $noticia->id,
                'tipo' => $tipo,
                'url' => Storage::disk('public')->url($path),
                'ordem' => $ordem,
            ]);
        }

        Cache::forget('noticias.index');

        Auditoria::registrar(
            'adicionou_midia_noticia',
            descricao: 'Adicionou ' . count($request->file('midias')) . " mídia(s) a \"{$noticia->titulo}\"",
            entidade: 'noticia',
            entidadeId: $noticia->id
        );

        return new NoticiaResource($noticia->fresh('midias'));
    }

    public function destroy(string $id, string $midiaId)
    {
        $midia = NoticiaMidia::where('noticia_id', $id)->findOrFail($midiaId);

        $path = preg_replace('#^.*/storage/#', '', $midia->url);
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $midia->delete();
        Cache::forget('noticias.index');

        Auditoria::registrar(
            'removeu_midia_noticia',
            descricao: "Removeu uma mídia da notícia #{$id}",
            entidade: 'noticia',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }
}
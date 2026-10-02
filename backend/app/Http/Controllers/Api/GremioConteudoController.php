<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GremioConteudoResource;
use App\Models\GremioConteudo;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class GremioConteudoController extends Controller
{
    private const CACHE_KEY = 'gremio_conteudos.index';

    public function index()
    {
        $rows = Cache::remember(self::CACHE_KEY, 300, fn () =>
            GremioConteudo::where('ativo', true)
                ->orderBy('ordem')->orderBy('id')
                ->get()
                ->toArray()
        );

        return GremioConteudoResource::collection(GremioConteudo::hydrate($rows));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required_without:arquivo_pdf|nullable|string|max:5000',
            'arquivo_pdf' => 'nullable|file|mimetypes:application/pdf,application/x-pdf|max:10240',
        ]);

        $data['conteudo'] = $data['conteudo'] ?? '';
        $data['ordem'] = (int) GremioConteudo::max('ordem') + 1;
        $data['criado_por'] = $request->user()->id;

        if ($request->hasFile('arquivo_pdf')) {
            $data['arquivo_pdf'] = $this->storeArquivo($request->file('arquivo_pdf'));
        }

        $item = GremioConteudo::create($data);
        Cache::forget(self::CACHE_KEY);

        Auditoria::registrar(
            'criou_conteudo_gremio',
            descricao: "Criou o conteúdo institucional \"{$item->titulo}\"",
            entidade: 'gremio_conteudo',
            entidadeId: $item->id
        );

        return (new GremioConteudoResource($item))->response()->setStatusCode(201);
    }

    public function update(Request $request, string $id)
    {
        $item = GremioConteudo::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'conteudo' => 'sometimes|nullable|string|max:5000',
            'arquivo_pdf' => 'sometimes|nullable|file|mimetypes:application/pdf,application/x-pdf|max:10240',
            'remover_arquivo_pdf' => 'sometimes|boolean',
        ]);

        if (array_key_exists('conteudo', $data)) {
            $data['conteudo'] = $data['conteudo'] ?? '';
        }

        $substituiuArquivo = $request->hasFile('arquivo_pdf');
        $removeuArquivo = ! $substituiuArquivo && $request->boolean('remover_arquivo_pdf');
        unset($data['remover_arquivo_pdf']);

        if ($substituiuArquivo) {
            $this->deleteArquivo($item->arquivo_pdf);
            $data['arquivo_pdf'] = $this->storeArquivo($request->file('arquivo_pdf'));
        } elseif ($removeuArquivo) {
            $this->deleteArquivo($item->arquivo_pdf);
            $data['arquivo_pdf'] = null;
        }

        $item->update($data);
        Cache::forget(self::CACHE_KEY);

        Auditoria::registrar(
            'editou_conteudo_gremio',
            descricao: "Editou o conteúdo institucional \"{$item->titulo}\"",
            entidade: 'gremio_conteudo',
            entidadeId: $item->id
        );

        return new GremioConteudoResource($item);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'id_a' => 'required|exists:gremio_conteudos,id',
            'id_b' => 'required|exists:gremio_conteudos,id',
        ]);

        $a = GremioConteudo::findOrFail($data['id_a']);
        $b = GremioConteudo::findOrFail($data['id_b']);

        [$ordemA, $ordemB] = [$a->ordem, $b->ordem];
        $a->update(['ordem' => $ordemB]);
        $b->update(['ordem' => $ordemA]);
        Cache::forget(self::CACHE_KEY);

        Auditoria::registrar(
            'reordenou_conteudo_gremio',
            descricao: "Trocou a ordem entre \"{$a->titulo}\" e \"{$b->titulo}\"",
            entidade: 'gremio_conteudo'
        );

        return $this->index();
    }

    public function destroy(string $id)
    {
        $item = GremioConteudo::findOrFail($id);
        $titulo = $item->titulo;
        $this->deleteArquivo($item->arquivo_pdf);
        $item->delete();
        Cache::forget(self::CACHE_KEY);

        Auditoria::registrar(
            'excluiu_conteudo_gremio',
            descricao: "Excluiu o conteúdo institucional \"{$titulo}\"",
            entidade: 'gremio_conteudo',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }

    private function storeArquivo(UploadedFile $file): string
    {
        $path = $file->store('gremio', 'public');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
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
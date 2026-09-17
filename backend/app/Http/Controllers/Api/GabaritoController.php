<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GabaritoResource;
use App\Models\Gabarito;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class GabaritoController extends Controller
{
    public function index()
    {
        $rows = Cache::remember('gabarito.index', 300, function () {
            return Gabarito::where('ativo', true)->orderBy('data_prova', 'desc')->get()->toArray();
        });

        return GabaritoResource::collection(Gabarito::hydrate($rows));
    }
    public function show(string $id)
    {
        return new GabaritoResource(Gabarito::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'disciplina' => 'required|string|max:255',
            'serie' => 'nullable|string|max:20',
            'tipo_prova' => 'required|string|max:100',
            'tipo_documento' => 'required|in:gabarito,prova',
            'data_prova' => 'required|date',
            'arquivo' => 'required|file|mimetypes:application/pdf,application/x-pdf|max:10240',
            'ativo' => 'boolean',
        ]);

        $data['publicado_por'] = $request->user()->id;
        $data['documento_url'] = $this->storeArquivo($request->file('arquivo'));
        Cache::forget('gabarito.index');

        $gabarito = Gabarito::create($data);

        Auditoria::registrar(
            'criou_gabarito',
            descricao: "Cadastrou {$gabarito->tipo_documento} \"{$gabarito->titulo}\"",
            entidade: 'gabarito',
            entidadeId: $gabarito->id
        );

        return new GabaritoResource($gabarito);
    }

    public function update(Request $request, string $id)
    {
        $gabarito = Gabarito::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'disciplina' => 'sometimes|required|string|max:255',
            'serie' => 'nullable|string|max:20',
            'tipo_prova' => 'sometimes|required|string|max:100',
            'tipo_documento' => 'sometimes|required|in:gabarito,prova',
            'data_prova' => 'sometimes|required|date',
            'arquivo' => 'sometimes|file|mimetypes:application/pdf,application/x-pdf|max:10240',
            'ativo' => 'boolean',
        ]);

        $substituiuArquivo = $request->hasFile('arquivo');

        if ($substituiuArquivo) {
            $this->deleteArquivoAntigo($gabarito->documento_url);
            $data['documento_url'] = $this->storeArquivo($request->file('arquivo'));
        }

        $gabarito->update($data);
        Cache::forget('gabarito.index');

        Auditoria::registrar(
            $substituiuArquivo ? 'substituiu_arquivo_gabarito' : 'editou_gabarito',
            descricao: ($substituiuArquivo ? 'Substituiu o arquivo de ' : 'Editou ') . "\"{$gabarito->titulo}\"",
            entidade: 'gabarito',
            entidadeId: $gabarito->id
        );

        return new GabaritoResource($gabarito);
    }

    public function destroy(string $id)
    {
        $gabarito = Gabarito::findOrFail($id);
        $titulo = $gabarito->titulo;
        $this->deleteArquivoAntigo($gabarito->documento_url);
        $gabarito->delete();
        Cache::forget('gabarito.index');

        Auditoria::registrar(
            'excluiu_gabarito',
            descricao: "Excluiu \"{$titulo}\"",
            entidade: 'gabarito',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }

    private function storeArquivo(UploadedFile $file): string
    {
        $path = $file->store('gabarito', 'public');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
    }

    private function deleteArquivoAntigo(?string $url): void
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
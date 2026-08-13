<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GabaritoResource;
use App\Models\Gabarito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GabaritoController extends Controller
{
    public function index()
    {
        return GabaritoResource::collection(
            Gabarito::where('ativo', true)->orderBy('data_prova', 'desc')->get()
        );
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
            'data_prova' => 'required|date',
            // Upload real do PDF. 10MB de limite.
            'arquivo' => 'required|file|mimetypes:application/pdf,application/x-pdf|max:10240',
            'ativo' => 'boolean',
        ]);

        $data['publicado_por'] = $request->user()->id;
        $data['tipo_documento'] = 'pdf';
        $data['documento_url'] = $this->storeArquivo($request->file('arquivo'));

        return new GabaritoResource(Gabarito::create($data));
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
            'data_prova' => 'sometimes|required|date',
            // Opcional aqui: só manda 'arquivo' quando for SUBSTITUIR o PDF.
            'arquivo' => 'sometimes|file|mimetypes:application/pdf,application/x-pdf|max:10240',
            'ativo' => 'boolean',
        ]);

        if ($request->hasFile('arquivo')) {
            $this->deleteArquivoAntigo($gabarito->documento_url);
            $data['documento_url'] = $this->storeArquivo($request->file('arquivo'));
            $data['tipo_documento'] = 'pdf';
        }

        $gabarito->update($data);

        return new GabaritoResource($gabarito);
    }

    public function destroy(string $id)
    {
        $gabarito = Gabarito::findOrFail($id);
        $this->deleteArquivoAntigo($gabarito->documento_url);
        $gabarito->delete();

        return response()->noContent();
    }

    /**
     * Salva o PDF no disk 'public' (storage/app/public/gabarito) e retorna
     * a URL pública (/storage/gabarito/xxx.pdf), servida sem download
     * forçado — o navegador abre o PDF direto, sem precisar de programa
     * instalado. Para baixar, basta o botão "download" no <a> do frontend.
     */
    private function storeArquivo($file): string
    {
        $path = $file->store('gabarito', 'public');

        return Storage::disk('public')->url($path);
    }

    /**
     * Ao substituir/excluir, apaga o PDF antigo do disco para não acumular
     * lixo em storage/app/public/gabarito.
     */
    private function deleteArquivoAntigo(?string $url): void
    {
        if (! $url) {
            return;
        }

        // Storage::disk('public')->url() gera algo como /storage/gabarito/x.pdf
        // (ou http://host/storage/gabarito/x.pdf). Extraímos o caminho relativo
        // ao disk 'public' (tudo depois de "/storage/").
        $path = preg_replace('#^.*/storage/#', '', $url);

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
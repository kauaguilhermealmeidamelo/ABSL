<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransparenciaResource;
use App\Models\Transparencia;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TransparenciaController extends Controller
{
    public function index()
    {
        $rows = Cache::remember('transparencia.index', 300, function () {
            return Transparencia::where('ativo', true)->orderBy('data_documento', 'desc')->get()->toArray();
        });

        return TransparenciaResource::collection(Transparencia::hydrate($rows));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria' => 'required|string|max:100',
            'arquivo_url' => 'nullable|string',
            'file' => 'nullable|file|mimetypes:application/pdf,application/x-pdf',
            'tipo_documento' => 'required|string|max:100',
            'data_documento' => 'required|date',
            'ativo' => 'boolean',
        ]);

        $data = Arr::only($data, [
            'titulo',
            'descricao',
            'categoria',
            'arquivo_url',
            'tipo_documento',
            'data_documento',
            'ativo',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/transparencia');
            $data['arquivo_url'] = Storage::url($path);
        }

        $data['publicado_por'] = $request->user() ? $request->user()->id : null;

        Cache::forget('transparencia.index');

        return new TransparenciaResource(Transparencia::create($data));
    }

    public function update(Request $request, string $id)
    {
        $transparencia = Transparencia::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'categoria' => 'sometimes|required|string|max:100',
            'arquivo_url' => 'nullable|string',
            'file' => 'nullable|file|mimetypes:application/pdf,application/x-pdf',
            'tipo_documento' => 'sometimes|required|string|max:100',
            'data_documento' => 'sometimes|required|date',
            'ativo' => 'boolean',
        ]);

        $data = Arr::only($data, [
            'titulo',
            'descricao',
            'categoria',
            'arquivo_url',
            'tipo_documento',
            'data_documento',
            'ativo',
        ]);

        if ($request->hasFile('file')) {
            $this->deleteArquivoAntigo($transparencia->arquivo_url);
            $path = $request->file('file')->store('public/transparencia');
            $data['arquivo_url'] = Storage::url($path);
        }

        $transparencia->update($data);
        Cache::forget('transparencia.index');

        return new TransparenciaResource($transparencia);
    }

    public function destroy(string $id)
    {
        $transparencia = Transparencia::findOrFail($id);
        $this->deleteArquivoAntigo($transparencia->arquivo_url);
        $transparencia->delete();
        Cache::forget('transparencia.index');

        return response()->noContent();
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
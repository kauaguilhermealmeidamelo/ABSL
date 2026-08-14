<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransparenciaResource;
use App\Models\Transparencia;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class TransparenciaController extends Controller
{
    public function index()
    {
        return TransparenciaResource::collection(
            Transparencia::where('ativo', true)->orderBy('data_documento', 'desc')->get()
        );
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
        'titulo', 'descricao', 'categoria', 'arquivo_url',
        'tipo_documento', 'data_documento', 'ativo',
    ]);

    if ($request->hasFile('file')) {
        $this->deleteArquivoAntigo($transparencia->arquivo_url);
        $path = $request->file('file')->store('public/transparencia');
        $data['arquivo_url'] = Storage::url($path);
    }

    $transparencia->update($data);

    return new TransparenciaResource($transparencia);
}

public function destroy(string $id)
{
    $transparencia = Transparencia::findOrFail($id);
    $this->deleteArquivoAntigo($transparencia->arquivo_url);
    $transparencia->delete();

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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transparencia;
use Illuminate\Http\Request;

class TransparenciaController extends Controller
{
    /**
     * Lista documentos de transparência ativos, mais recentes primeiro.
     */
    public function index()
    {
        return Transparencia::where('ativo', true)
            ->orderBy('data_documento', 'desc')
            ->get();
    }

    /**
     * Exibe um documento específico.
     */
    public function show(string $id)
    {
        return Transparencia::findOrFail($id);
    }

    /**
     * Cria um novo documento (ata ou prestação de contas).
     * Protegido por 'auth:sanctum'.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria' => 'required|string|max:100',
            'arquivo_url' => 'nullable|string',
            'tipo_documento' => 'required|string|max:100',
            'data_documento' => 'required|date',
            'ativo' => 'boolean',
        ]);

        $data['publicado_por'] = $request->user()->id;

        return response()->json(Transparencia::create($data), 201);
    }

    /**
     * Atualiza um documento existente. Protegido por 'auth:sanctum'.
     */
    public function update(Request $request, string $id)
    {
        $transparencia = Transparencia::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'categoria' => 'sometimes|required|string|max:100',
            'arquivo_url' => 'nullable|string',
            'tipo_documento' => 'sometimes|required|string|max:100',
            'data_documento' => 'sometimes|required|date',
            'ativo' => 'boolean',
        ]);

        $transparencia->update($data);

        return $transparencia;
    }

    /**
     * Remove um documento. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Transparencia::findOrFail($id)->delete();

        return response()->noContent();
    }
}   
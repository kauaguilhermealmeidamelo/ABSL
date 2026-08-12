<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GabaritoResource;
use App\Models\Gabarito;
use Illuminate\Http\Request;

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
            'documento_url' => 'required|string',
            'tipo_documento' => 'required|string|max:20',
            'ativo' => 'boolean',
        ]);

        $data['publicado_por'] = $request->user()->id;

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
            'documento_url' => 'sometimes|required|string',
            'tipo_documento' => 'sometimes|required|string|max:20',
            'ativo' => 'boolean',
        ]);

        $gabarito->update($data);

        return new GabaritoResource($gabarito);
    }

    public function destroy(string $id)
    {
        Gabarito::findOrFail($id)->delete();

        return response()->noContent();
    }
}
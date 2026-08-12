<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cardapio;
use Illuminate\Http\Request;

class CardapioController extends Controller
{
    /**
     * Lista o cardápio ativo, ordenado por data.
     */
    public function index()
    {
        return Cardapio::where('ativo', true)
            ->orderBy('data', 'asc')
            ->get();
    }

    /**
     * Exibe um item do cardápio específico.
     */
    public function show(string $id)
    {
        return Cardapio::findOrFail($id);
    }

    /**
     * Cria um novo item de cardápio. Protegido por 'auth:sanctum'.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'data' => 'required|date',
            'dia_semana' => 'required|string|max:20',
            'refeicao' => 'required|string',
            'descricao' => 'required|string',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $data['criado_por'] = $request->user()->id;

        return response()->json(Cardapio::create($data), 201);
    }

    /**
     * Atualiza um item de cardápio existente. Protegido por 'auth:sanctum'.
     */
    public function update(Request $request, string $id)
    {
        $cardapio = Cardapio::findOrFail($id);

        $data = $request->validate([
            'data' => 'sometimes|required|date',
            'dia_semana' => 'sometimes|required|string|max:20',
            'refeicao' => 'sometimes|required|string',
            'descricao' => 'sometimes|required|string',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $cardapio->update($data);

        return $cardapio;
    }

    /**
     * Remove um item de cardápio. Protegido por 'auth:sanctum'.
     */
    public function destroy(string $id)
    {
        Cardapio::findOrFail($id)->delete();

        return response()->noContent();
    }
}
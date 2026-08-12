<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cardapio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CardapioController extends Controller
{
    /**
     * Lista o cardápio ativo, ordenado por data.
     */
    public function index()
    {
        // Return active menu items including date and description for the weekly menu.
        return Cardapio::where('ativo', true)
            ->orderBy('data', 'asc')
            ->get(['id', 'data', 'dia_semana', 'refeicao', 'descricao', 'observacoes', 'ativo']);
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
            'data' => 'nullable|date',
            'dia_semana' => 'required|string|max:50',
            'refeicao' => 'required|string',
            'descricao' => 'required|string',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        if (empty($data['data'])) {
            $data['data'] = now()->toDateString();
        }

        $data['criado_por'] = $request->user() ? $request->user()->id : null;

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
            'dia_semana' => 'sometimes|required|string|max:50',
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
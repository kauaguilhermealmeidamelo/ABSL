<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardapioResource;
use App\Models\Cardapio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardapioController extends Controller
{
    public function index()
    {
        return CardapioResource::collection(
            Cache::remember('cardapio.index', 300, function () {
                return Cardapio::where('ativo', true)->orderBy('data', 'asc')->get();
            })
        );
    }

    public function show(string $id)
    {
        return new CardapioResource(Cardapio::findOrFail($id));
    }

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
        Cache::forget('cardapio.index');
        return new CardapioResource(Cardapio::create($data));
    }

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
        Cache::forget('cardapio.index');
        return new CardapioResource($cardapio);
    }

    public function destroy(string $id)
    {
        Cardapio::findOrFail($id)->delete();
        Cache::forget('cardapio.index');
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardapioResource;
use App\Models\Cardapio;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardapioController extends Controller
{
    public function index()
    {
        $rows = Cache::remember('cardapio.index', 300, fn () =>
            Cardapio::where('ativo', true)
                ->orderBy('data', 'asc')
                ->get()
                ->toArray()
        );
        return CardapioResource::collection(Cardapio::hydrate($rows));
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

        $cardapio = Cardapio::create($data);

        Auditoria::registrar(
            'criou_cardapio',
            descricao: "Cadastrou o cardápio de {$cardapio->dia_semana}",
            entidade: 'cardapio',
            entidadeId: $cardapio->id
        );

        return new CardapioResource($cardapio);
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

        Auditoria::registrar(
            'editou_cardapio',
            descricao: "Editou o cardápio de {$cardapio->dia_semana}",
            entidade: 'cardapio',
            entidadeId: $cardapio->id
        );

        return new CardapioResource($cardapio);
    }

    public function destroy(string $id)
    {
        $cardapio = Cardapio::findOrFail($id);
        $dia = $cardapio->dia_semana;
        $cardapio->delete();
        Cache::forget('cardapio.index');

        Auditoria::registrar(
            'excluiu_cardapio',
            descricao: "Excluiu o cardápio de {$dia}",
            entidade: 'cardapio',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diretoria;
use Illuminate\Http\Request;

class DiretoriaController extends Controller
{
    public function index()
    {
        return Diretoria::where('ativo', true)->orderBy('ordem')->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'members' => 'nullable|array',
        ]);

        $data['criado_por'] = $request->user() ? $request->user()->id : null;
        // Nova diretoria sempre entra no fim da ordem atual.
        $data['ordem'] = (int) Diretoria::max('ordem') + 1;

        return response()->json(Diretoria::create($data), 201);
    }

    public function update(Request $request, string $id)
    {
        $dir = Diretoria::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'ordem' => 'sometimes|integer|min:0',
            'ativo' => 'boolean',
        ]);
        $dir->update($data);
        return $dir;
    }

    /**
     * Troca a posição de duas diretorias (swap de 'ordem'), usado pelos
     * botões de mover para cima/baixo no painel de gerenciamento.
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'id_a' => 'required|exists:diretorias,id',
            'id_b' => 'required|exists:diretorias,id',
        ]);

        $a = Diretoria::findOrFail($data['id_a']);
        $b = Diretoria::findOrFail($data['id_b']);

        [$ordemA, $ordemB] = [$a->ordem, $b->ordem];
        $a->update(['ordem' => $ordemB]);
        $b->update(['ordem' => $ordemA]);

        return Diretoria::where('ativo', true)->orderBy('ordem')->orderBy('name')->get();
    }

    public function destroy(string $id)
    {
        Diretoria::findOrFail($id)->delete();
        return response()->noContent();
    }
}
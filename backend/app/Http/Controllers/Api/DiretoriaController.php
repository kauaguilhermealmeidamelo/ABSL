<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diretoria;
use App\Support\Auditoria;
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
        $data['ordem'] = (int) Diretoria::max('ordem') + 1;

        $dir = Diretoria::create($data);

        Auditoria::registrar(
            'criou_diretoria',
            descricao: "Criou a diretoria \"{$dir->name}\"",
            entidade: 'diretoria',
            entidadeId: $dir->id
        );

        return response()->json($dir, 201);
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

        Auditoria::registrar(
            'editou_diretoria',
            descricao: "Editou a diretoria \"{$dir->name}\"",
            entidade: 'diretoria',
            entidadeId: $dir->id
        );

        return $dir;
    }

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

        Auditoria::registrar(
            'reordenou_diretorias',
            descricao: "Trocou a ordem entre \"{$a->name}\" e \"{$b->name}\"",
            entidade: 'diretoria'
        );

        return Diretoria::where('ativo', true)->orderBy('ordem')->orderBy('name')->get();
    }

    public function destroy(string $id)
    {
        $dir = Diretoria::findOrFail($id);
        $nome = $dir->name;
        $dir->delete();

        Auditoria::registrar(
            'excluiu_diretoria',
            descricao: "Excluiu a diretoria \"{$nome}\"",
            entidade: 'diretoria',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }
}
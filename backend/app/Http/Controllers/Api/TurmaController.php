<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turma;
use App\Support\Auditoria;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index()
    {
        return Turma::where('ativo', true)->orderBy('turno')->orderBy('ano')->orderBy('codigo')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'turno' => 'required|string|max:50',
            'ano' => 'required|string|max:50',
            'codigo' => 'required|string|max:10|unique:turmas,codigo',
        ]);

        $data['criado_por'] = $request->user() ? $request->user()->id : null;

        $turma = Turma::create($data);

        Auditoria::registrar(
            'criou_turma',
            descricao: "Criou a turma {$turma->codigo} ({$turma->turno}, {$turma->ano})",
            entidade: 'turma',
            entidadeId: $turma->id
        );

        return response()->json($turma, 201);
    }

    public function destroy(string $codigo)
    {
        $turma = Turma::where('codigo', $codigo)->firstOrFail();
        $turma->delete();

        Auditoria::registrar(
            'excluiu_turma',
            descricao: "Excluiu a turma {$codigo}",
            entidade: 'turma'
        );

        return response()->noContent();
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turma;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index()
    {
        // Return turmas persisted in DB
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

        return response()->json(Turma::create($data), 201);
    }

    public function destroy(string $codigo)
    {
        $turma = Turma::where('codigo', $codigo)->firstOrFail();
        $turma->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HorarioResource;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HorarioController extends Controller
{
    public function index()
    {
        return HorarioResource::collection(
            Horario::where('ativo', true)
                ->orderBy('turma')->orderBy('dia_semana')->orderBy('horario_inicio')
                ->get()
        );
    }

    public function show(string $turma)
    {
        return HorarioResource::collection(
            Horario::where('turma', $turma)->where('ativo', true)
                ->orderBy('dia_semana')->orderBy('horario_inicio')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'turma' => 'required|string|max:10',
            'dia_semana' => 'required|string|max:20',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i',
            'disciplina' => 'required|string|max:255',
            'professor' => 'nullable|string|max:255',
            'sala' => 'nullable|string|max:50',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $data = Arr::only($validated, [
            'turma', 'dia_semana', 'horario_inicio', 'horario_fim',
            'disciplina', 'professor', 'sala', 'observacoes', 'ativo',
        ]);

        $data['criado_por'] = $request->user()->id;

        $horario = Horario::updateOrCreate(
            [
                'turma' => $data['turma'],
                'dia_semana' => $data['dia_semana'],
                'horario_inicio' => $data['horario_inicio'],
            ],
            $data
        );

        return (new HorarioResource($horario))
            ->response()
            ->setStatusCode($horario->wasRecentlyCreated ? 201 : 200);
    }

    public function update(Request $request, string $id)
    {
        $horario = Horario::findOrFail($id);

        $data = $request->validate([
            'turma' => 'sometimes|required|string|max:10',
            'dia_semana' => 'sometimes|required|string|max:20',
            'horario_inicio' => 'sometimes|required|date_format:H:i',
            'horario_fim' => 'sometimes|required|date_format:H:i',
            'disciplina' => 'sometimes|required|string|max:255',
            'professor' => 'nullable|string|max:255',
            'sala' => 'nullable|string|max:50',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $horario->update($data);

        return new HorarioResource($horario);
    }

    public function destroy(string $id)
    {
        Horario::findOrFail($id)->delete();

        return response()->noContent();
    }
}
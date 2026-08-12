<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HorarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'turma' => $this->turma,
            'dia_semana' => $this->dia_semana,
            'horario_inicio' => $this->horario_inicio,
            'horario_fim' => $this->horario_fim,
            'disciplina' => $this->disciplina,
            'professor' => $this->professor,
            'sala' => $this->sala,
            'observacoes' => $this->observacoes,
            'ativo' => $this->ativo,
            // 'criado_por' omitido de propósito: endpoint é público.
        ];
    }
}
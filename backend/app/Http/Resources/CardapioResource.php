<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardapioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'dia_semana' => $this->dia_semana,
            'refeicao' => $this->refeicao,
            'descricao' => $this->descricao,
            'observacoes' => $this->observacoes,
            'ativo' => $this->ativo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'criado_por' omitido de propósito: endpoint é público.
        ];
    }
}
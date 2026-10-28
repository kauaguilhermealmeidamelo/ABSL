<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'local' => $this->local,
            'data_evento' => $this->data_evento?->toIso8601String(),
            'imagem_url' => $this->imagem_url,
            'link_ingresso' => $this->link_ingresso,
            'ativo' => $this->ativo,
            // 'autor_id' omitido de propósito: endpoint é público.
        ];
    }
}

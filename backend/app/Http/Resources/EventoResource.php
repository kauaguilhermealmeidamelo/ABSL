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
            // Horário "de parede" (sem fuso): o que foi digitado no formulário é o que
            // aparece. Com fuso (+00:00) o navegador em Brasília mostrava 3h a menos.
            'data_evento' => $this->data_evento?->format('Y-m-d\TH:i:s'),
            'imagem_url' => $this->imagem_url,
            'link_ingresso' => $this->link_ingresso,
            'preco' => $this->preco !== null ? (float) $this->preco : null,
            'gratuito' => (bool) $this->gratuito,
            'ativo' => $this->ativo,
            // 'autor_id' omitido de propósito: endpoint é público.
        ];
    }
}

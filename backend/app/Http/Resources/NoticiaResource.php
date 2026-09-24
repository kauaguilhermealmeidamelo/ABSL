<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'categoria' => $this->categoria,
            'descricao' => $this->descricao,
            'conteudo' => $this->conteudo,
            'imagem_url' => $this->imagem_url,
            'data_publicacao' => $this->data_publicacao,
            'destaque' => $this->destaque,
            'ativo' => $this->ativo,
            'curtidas_count' => $this->curtidas_count ?? 0,
            'comentarios_count' => $this->comentarios_count ?? 0,
            'curtido' => (bool) ($this->curtido ?? false),
            // 'autor_id' omitido de propósito: endpoint é público.
        ];
    }
}
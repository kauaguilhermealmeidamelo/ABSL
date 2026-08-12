<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GabaritoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'disciplina' => $this->disciplina,
            'serie' => $this->serie,
            'tipo_prova' => $this->tipo_prova,
            'data_prova' => $this->data_prova,
            'documento_url' => $this->documento_url,
            'tipo_documento' => $this->tipo_documento,
            'data_publicacao' => $this->data_publicacao,
            'data_atualizacao' => $this->data_atualizacao,
            'ativo' => $this->ativo,
            // 'publicado_por' omitido de propósito: endpoint é público.
        ];
    }
}
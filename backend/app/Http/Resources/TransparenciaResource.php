<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransparenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'categoria' => $this->categoria,
            'arquivo_url' => $this->arquivo_url,
            'tipo_documento' => $this->tipo_documento,
            'data_documento' => $this->data_documento,
            'ativo' => $this->ativo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'publicado_por' omitido de propósito: endpoint é público.
        ];
    }
}
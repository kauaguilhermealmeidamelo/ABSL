<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjetoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'diretoria' => $this->diretoria,
            'categoria' => $this->categoria,
            'imagem_url' => $this->imagem_url,
            'conteudo_detalhado' => $this->conteudo_detalhado,
            'status' => $this->status,
            'data_conclusao' => $this->data_conclusao,
            'destaque' => $this->destaque,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // 'responsavel_id' omitido de propósito: endpoint é público.
        ];
    }
}
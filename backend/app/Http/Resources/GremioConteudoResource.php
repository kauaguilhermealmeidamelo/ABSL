<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GremioConteudoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'conteudo' => $this->conteudo,
            // URL pública do PDF no disco 'public' (pasta gremio/), ou null.
            'arquivo_pdf' => $this->arquivo_pdf,
            'ordem' => $this->ordem,
            'updated_at' => $this->updated_at,
            // 'criado_por' omitido de propósito: endpoint é público.
        ];
    }
}
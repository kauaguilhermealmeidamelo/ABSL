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
            'midias' => $this->midiasParaExibicao(),
            'data_publicacao' => $this->data_publicacao,
            'destaque' => $this->destaque,
            'ativo' => $this->ativo,
            'curtidas_count' => $this->curtidas_count ?? 0,
            'comentarios_count' => $this->comentarios_count ?? 0,
            'curtido' => (bool) ($this->curtido ?? false),
            // 'autor_id' omitido de propósito: endpoint é público.
        ];
    }

    private function midiasParaExibicao(): array
    {
        if ($this->relationLoaded('midias') && $this->midias->isNotEmpty()) {
            return $this->midias->map(fn ($m) => [
                'id' => $m->id,
                'tipo' => $m->tipo,
                'url' => $m->url,
            ])->values()->all();
        }

        if ($this->midias) {
            return [['id' => null, 'tipo' => 'imagem', 'url' => $this->midias]];
        }

        return [];
    }
}
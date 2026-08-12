<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $fillable = [
        'titulo',
        'categoria',
        'descricao',
        'conteudo',
        'imagem_url',
        'data_publicacao',
        'destaque',
        'ativo',
        'autor_id',
    ];

    protected $casts = [
        'data_publicacao' => 'datetime',
        'destaque' => 'boolean',
        'ativo' => 'boolean',
    ];
}   
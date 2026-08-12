<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'diretoria',
        'categoria',
        'imagem_url',
        'conteudo_detalhado',
        'status',
        'data_conclusao',
        'destaque',
        'responsavel_id',
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
    ];
}
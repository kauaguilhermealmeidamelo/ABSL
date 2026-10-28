<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'local',
        'data_evento',
        'imagem_url',
        'link_ingresso',
        'ativo',
        'autor_id',
    ];

    protected $casts = [
        'data_evento' => 'datetime',
        'ativo' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ouvinte extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'turma',
        'mensagem',
        'tipo',
        'anonimo',
        'status',
        'resposta',
        'data_resposta',
        'respondido_por',
        'data_fechamento',
    ];

    protected $casts = [
        'anonimo' => 'boolean',
        'data_resposta' => 'datetime',
        'data_fechamento' => 'datetime',
    ];
}

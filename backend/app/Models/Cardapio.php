<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cardapio extends Model
{
    protected $fillable = [
        'data',
        'dia_semana',
        'refeicao',
        'descricao',
        'observacoes',
        'ativo',
        'criado_por',
    ];

    protected $casts = [
        'data' => 'date',
        'ativo' => 'boolean',
    ];
}
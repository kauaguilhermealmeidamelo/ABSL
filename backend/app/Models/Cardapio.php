<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cardapio extends Model
{
    protected $table = 'cardapio';
    protected $fillable = ['data', 'dia_semana', 'refeicao', 'descricao', 'observacoes', 'criado_por', 'ativo'];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}
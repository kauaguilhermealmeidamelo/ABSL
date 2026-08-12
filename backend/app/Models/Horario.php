<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    protected $fillable = [
        'turma',
        'dia_semana',
        'horario_inicio',
        'horario_fim',
        'disciplina',
        'professor',
        'sala',
        'observacoes',
        'ativo',
        'criado_por',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}
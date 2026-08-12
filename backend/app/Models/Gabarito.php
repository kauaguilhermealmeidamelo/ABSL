<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gabarito extends Model
{
    protected $table = 'gabarito';
    protected $fillable = [
        'titulo',
        'descricao',
        'disciplina',
        'serie',
        'tipo_prova',
        'data_prova',
        'documento_url',
        'tipo_documento',
        'ativo',
        'publicado_por',
    ];

    protected $casts = [
        'data_prova' => 'date',
        'data_publicacao' => 'datetime',
        'ativo' => 'boolean',
    ];
}
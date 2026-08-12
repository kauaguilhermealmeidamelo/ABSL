<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transparencia extends Model
{
    protected $table = 'transparencia';

    protected $fillable = [
        'titulo',
        'descricao',
        'categoria',
        'arquivo_url',
        'tipo_documento',
        'data_documento',
        'ativo',
        'publicado_por',
    ];
}

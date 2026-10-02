<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GremioConteudo extends Model
{
    protected $table = 'gremio_conteudos';

    protected $fillable = ['titulo', 'conteudo', 'arquivo_pdf', 'ordem', 'ativo', 'criado_por'];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];
}
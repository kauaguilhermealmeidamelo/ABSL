<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretoria extends Model
{
    use HasFactory;

    protected $casts = [
        'members' => 'array',
    ];

    protected $fillable = ['name', 'icon', 'members', 'criado_por', 'ativo'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InicioMedia extends Model
{
    protected $table = 'inicio_media';
    use HasFactory;

    protected $fillable = ['file_name', 'url', 'criado_por', 'ativo'];
}

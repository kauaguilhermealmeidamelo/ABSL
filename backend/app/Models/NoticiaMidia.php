<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaMidia extends Model
{
    protected $fillable = ['noticia_id', 'tipo', 'url', 'ordem'];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }
}
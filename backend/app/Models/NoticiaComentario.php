<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaComentario extends Model
{
    protected $fillable = ['noticia_id', 'user_id', 'texto'];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
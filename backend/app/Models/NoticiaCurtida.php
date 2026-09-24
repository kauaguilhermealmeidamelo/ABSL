<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaCurtida extends Model
{
    protected $fillable = ['noticia_id', 'user_id'];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
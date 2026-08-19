<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    public $timestamps = false;

    protected $fillable = ['ip', 'path', 'user_agent', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Visita $visita) {
            $visita->created_at ??= now();
        });
    }
}   
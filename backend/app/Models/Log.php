<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'acao',
        'entidade',
        'entidade_id',
        'descricao',
        'user_id',
        'user_nome',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Log $log) {
            $log->created_at ??= now();
        });
    }
}
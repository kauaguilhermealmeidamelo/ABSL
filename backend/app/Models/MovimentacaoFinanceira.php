<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentacaoFinanceira extends Model
{
    protected $table = 'movimentacoes_financeiras';

    protected $fillable = ['gestao_financeira_id', 'data', 'tipo', 'categoria', 'descricao', 'valor_centavos', 'status', 'criada_por'];

    protected function casts(): array
    {
        return ['data' => 'date'];
    }

    public function gestao(): BelongsTo
    {
        return $this->belongsTo(GestaoFinanceira::class, 'gestao_financeira_id');
    }
}

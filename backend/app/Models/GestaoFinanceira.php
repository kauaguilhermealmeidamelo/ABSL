<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class GestaoFinanceira extends Model
{
    protected $table = 'gestoes_financeiras';
    protected $fillable = ['nome','inicio','fim_previsto','saldo_inicial_centavos','status','criada_por','encerrada_em','encerrada_por','observacao_encerramento','saldo_real_final_centavos','diferenca_conciliacao_centavos','justificativa_conciliacao','conciliado_em','conciliado_por','ativada_em','ativada_por'];
    protected function casts(): array { return ['inicio'=>'date','fim_previsto'=>'date','encerrada_em'=>'date','conciliado_em'=>'datetime','ativada_em'=>'datetime']; }
    public function movimentacoes(): HasMany { return $this->hasMany(MovimentacaoFinanceira::class,'gestao_financeira_id'); }
}

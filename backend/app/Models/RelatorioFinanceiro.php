<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RelatorioFinanceiro extends Model {
 protected $table = 'relatorios_financeiros';
 protected $guarded = ['id'];
 protected function casts(): array { return ['publicado_em' => 'datetime', 'fechado_em' => 'datetime']; }
}

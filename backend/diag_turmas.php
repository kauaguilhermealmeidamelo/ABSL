<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "== Comparação de conteúdo (absl_gremio vs absl_local) ==\n";
$tables = ['users', 'turmas', 'noticias', 'projetos', 'cardapio', 'horario', 'diretorias', 'gabarito', 'transparencia', 'ouvintes', 'visitas'];
foreach ($tables as $tbl) {
    $a = $b = 'sem tabela';
    try { $a = DB::select("SELECT COUNT(*) AS n FROM absl_gremio.`{$tbl}`")[0]->n; } catch (\Throwable $e) {}
    try { $b = DB::select("SELECT COUNT(*) AS n FROM absl_local.`{$tbl}`")[0]->n; } catch (\Throwable $e) {}
    echo sprintf("  %-15s gremio=%-5s local=%s\n", $tbl, $a, $b);
}

echo "\n== Copiando turmas para absl_local (idempotente) ==\n";
DB::statement("
    INSERT INTO absl_local.turmas (turno, ano, codigo, criado_por, ativo, created_at, updated_at)
    SELECT g.turno, g.ano, g.codigo, NULL, g.ativo, g.created_at, g.created_at
    FROM absl_gremio.turmas g
    WHERE NOT EXISTS (SELECT 1 FROM absl_local.turmas l WHERE l.codigo = g.codigo)
");
echo 'turmas em absl_local agora: '.DB::table('turmas')->count()."\n";
foreach (DB::table('turmas')->orderBy('turno')->orderBy('ano')->orderBy('codigo')->get() as $t) {
    echo "  {$t->codigo} | {$t->turno} | {$t->ano} | ativo={$t->ativo}\n";
}

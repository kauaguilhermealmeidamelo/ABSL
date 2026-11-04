<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\FinanceiroController;
use App\Http\Controllers\Api\CicloGestaoFinanceiraController;
use App\Http\Controllers\Api\DeclaracaoInatividadeController;
use App\Http\Controllers\Api\PdfInatividadeController;
use App\Http\Controllers\Api\PdfRelatorioFinanceiroController;
use App\Http\Controllers\Api\TransicaoFinanceiraController;
use App\Http\Controllers\Api\MovimentacaoTransicaoController;
use App\Http\Controllers\Api\FechamentoTransicaoController;
use App\Http\Controllers\Api\CancelamentoFinanceiroController;
use App\Http\Controllers\Api\RetificacaoFinanceiraController;
use App\Http\Controllers\Api\EsclarecimentoFinanceiroController;
use App\Http\Controllers\Api\ConciliacaoFinanceiraController;
use App\Http\Controllers\Api\VinculoFinanceiroController;
use App\Http\Controllers\Api\RelatorioFinanceiroController;
use App\Http\Controllers\Api\ComprovanteFinanceiroController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    NoticiaController,
    EventoController,
    ProjetoController,
    CardapioController,
    OuvinteController,
    TransparenciaController,
    GabaritoController,
    HorarioController,
    TurmaController,
    DiretoriaController,
    InicioMediaController,
    AuthController,
    PasswordResetController,
    VisitaController,
    AdminUserController,
    LogController,
    NoticiaCurtidaController,
    NoticiaComentarioController,
    NoticiaMidiaController
};

// Rotas públicas de autenticação — throttle aqui, que é onde a requisição
// de fato chega (sem sessão).
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->middleware('throttle:5,1');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:6,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Verificação de e-mail oficial do Laravel (MustVerifyEmail).
// A SPA usa /api/user para saber se email_verified_at já foi preenchido;
// os links do e-mail apontam para rotas web assinadas abaixo.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1');
    Route::put('/me', [AuthController::class, 'updateProfile']);
});

Route::get('/noticias', [NoticiaController::class, 'index']);
Route::get('/noticias/{id}', [NoticiaController::class, 'show']);
Route::get('/noticias/{id}/comentarios', [NoticiaComentarioController::class, 'index']);
Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/{id}', [EventoController::class, 'show']);
Route::get('/projetos', [ProjetoController::class, 'index']);
Route::get('/projetos/{id}', [ProjetoController::class, 'show']);
Route::get('/cardapio', [CardapioController::class, 'index']);
Route::get('/cardapio/{id}', [CardapioController::class, 'show']);
Route::get('/gabarito', [GabaritoController::class, 'index']);
Route::get('/gabarito/{id}', [GabaritoController::class, 'show']);
Route::get('/horario', [HorarioController::class, 'index']);
Route::get('/horario/{turma}', [HorarioController::class, 'show']);
Route::get('/turmas', [TurmaController::class, 'index']);
Route::get('/diretorias', [DiretoriaController::class, 'index']);
Route::get('/inicio-media', [InicioMediaController::class, 'index']);
Route::get('/transparencia', [TransparenciaController::class, 'index']);
Route::get('/financeiro/relatorios', [RelatorioFinanceiroController::class, 'index']);
Route::get('/financeiro/gestoes/{gestao}/esclarecimentos', [EsclarecimentoFinanceiroController::class, 'index']);
Route::get('/financeiro/relatorios/{relatorio}/pdf', [PdfRelatorioFinanceiroController::class, 'download']);
Route::get('/financeiro/relatorios/{relatorio}/versoes/{versao}/pdf', [PdfRelatorioFinanceiroController::class, 'versao']);
Route::get('/financeiro/transicoes/{transicao}/relatorio', [FechamentoTransicaoController::class, 'relatorio']);
Route::get('/financeiro/gestoes/{gestao}/inatividade', [DeclaracaoInatividadeController::class, 'consultar']);
Route::get('/financeiro/gestoes/{gestao}/inatividade/pdf', [PdfInatividadeController::class, 'download']);
Route::get('/transparencia/{id}', [TransparenciaController::class, 'show']);
Route::post('/visitas', [VisitaController::class, 'store'])->middleware('throttle:30,1');

// ── Ouvidoria: rotas públicas ───────────────────────────────────────────
// IMPORTANTE: '/ouvintes/respondidas' e '/ouvintes/protocolo/{id}' têm que
// vir ANTES de qualquer rota com '{id}' genérico (inclusive a do grupo
// admin logo abaixo). O Laravel casa rotas na ordem em que são declaradas
// neste arquivo — se uma rota '/ouvintes/{id}' vier primeiro, ela "captura"
// a palavra 'respondidas' como se fosse um id e tenta buscar um registro
// com esse valor, gerando "No query results for model [Ouvinte] respondidas".
Route::post('/ouvintes', [OuvinteController::class, 'store']);
Route::get('/ouvintes/respondidas', [OuvinteController::class, 'respondidas']);
Route::get('/ouvintes/protocolo/{id}', [OuvinteController::class, 'consultarProtocolo']);

// GET /user: usado por checkSession() no frontend para validar a sessão
// junto ao backend. Precisa estar autenticado, mas não precisa ser admin.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// backend/routes/api.php
// ...tudo que já era 'admin' vira 'staff', exceto /admin/usuarios que fica 'admin'

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/noticias/{id}/curtir', [NoticiaCurtidaController::class, 'toggle']);
    Route::post('/noticias/{id}/comentarios', [NoticiaComentarioController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'staff'])->group(function () {
    Route::post('/noticias', [NoticiaController::class, 'store']);
    Route::put('/noticias/{id}', [NoticiaController::class, 'update']);
    Route::delete('/noticias/{id}', [NoticiaController::class, 'destroy']);
    Route::post('/noticias/{id}/midias', [NoticiaMidiaController::class, 'store']);
    Route::delete('/noticias/{id}/midias/{midiaId}', [NoticiaMidiaController::class, 'destroy']);
    Route::delete('/noticias/{id}/comentarios/{comentarioId}', [NoticiaComentarioController::class, 'destroy']);

    Route::post('/eventos', [EventoController::class, 'store']);
    Route::put('/eventos/{id}', [EventoController::class, 'update']);
    Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);

    Route::post('/projetos', [ProjetoController::class, 'store']);
    Route::put('/projetos/{id}', [ProjetoController::class, 'update']);
    Route::delete('/projetos/{id}', [ProjetoController::class, 'destroy']);

    Route::post('/cardapio', [CardapioController::class, 'store']);
    Route::put('/cardapio/{id}', [CardapioController::class, 'update']);
    Route::delete('/cardapio/{id}', [CardapioController::class, 'destroy']);

    Route::post('/gabarito', [GabaritoController::class, 'store']);
    Route::put('/gabarito/{id}', [GabaritoController::class, 'update']);
    Route::delete('/gabarito/{id}', [GabaritoController::class, 'destroy']);

    Route::post('/transparencia', [TransparenciaController::class, 'store']);
    Route::put('/transparencia/{id}', [TransparenciaController::class, 'update']);
    Route::delete('/transparencia/{id}', [TransparenciaController::class, 'destroy']);

    Route::post('/horario', [HorarioController::class, 'store']);
    Route::put('/horario/{id}', [HorarioController::class, 'update']);
    Route::delete('/horario/{id}', [HorarioController::class, 'destroy']);

    Route::post('/turmas', [TurmaController::class, 'store']);
    Route::delete('/turmas/{codigo}', [TurmaController::class, 'destroy']);

    Route::post('/diretorias', [DiretoriaController::class, 'store']);
    Route::put('/diretorias/{id}', [DiretoriaController::class, 'update']);
    Route::post('/diretorias/reorder', [DiretoriaController::class, 'reorder']);
    Route::delete('/diretorias/{id}', [DiretoriaController::class, 'destroy']);

    Route::post('/inicio-media', [InicioMediaController::class, 'store']);

    Route::get('/ouvintes', [OuvinteController::class, 'index']);
    Route::get('/ouvintes/{id}', [OuvinteController::class, 'show']);
    Route::put('/ouvintes/{id}', [OuvinteController::class, 'update']);
    Route::delete('/ouvintes/{id}', [OuvinteController::class, 'destroy']);

    Route::get('/visitas/estatisticas', [VisitaController::class, 'estatisticas']);
});

// Gestão financeira: dados completos restritos a administradores.
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/financeiro')->group(function () {
    Route::get('/gestoes', [FinanceiroController::class, 'gestoes']);
    Route::post('/gestoes/{gestao}/esclarecimentos', [EsclarecimentoFinanceiroController::class, 'publicar']);
    Route::get('/transicoes', [TransicaoFinanceiraController::class, 'index']);
    Route::post('/transicoes', [TransicaoFinanceiraController::class, 'iniciar']);
    Route::get('/transicoes/{transicao}/movimentacoes', [MovimentacaoTransicaoController::class, 'index']);
    Route::post('/transicoes/{transicao}/movimentacoes', [MovimentacaoTransicaoController::class, 'criar']);
    Route::post('/transicoes/{transicao}/movimentacoes/{movimentacao}/resolver', [MovimentacaoTransicaoController::class, 'resolver']);
    Route::post('/transicoes/{transicao}/encerrar', [FechamentoTransicaoController::class, 'encerrar']);
    Route::get('/gestoes/{gestao}/vinculos', [VinculoFinanceiroController::class, 'index']);
    Route::post('/gestoes/{gestao}/vinculos', [VinculoFinanceiroController::class, 'store']);
    Route::post('/gestoes/{gestao}/vinculos/{vinculo}/encerrar', [VinculoFinanceiroController::class, 'encerrar']);
    Route::post('/gestoes', [CicloGestaoFinanceiraController::class, 'planejar']);
    Route::post('/gestoes/{gestao}/ativar', [CicloGestaoFinanceiraController::class, 'ativar']);
    Route::post('/gestoes/{gestao}/inatividade/publicar', [DeclaracaoInatividadeController::class, 'publicar']);
    Route::post('/gestoes/{gestao}/encerrar', [FinanceiroController::class, 'encerrar']);
});


Route::middleware('auth:sanctum')->prefix('admin/financeiro')->group(function () {
    Route::get('/gestoes/{gestao}/resumo', [FinanceiroController::class, 'resumo']);
    Route::post('/gestoes/{gestao}/conciliacao', [ConciliacaoFinanceiraController::class, 'registrar']);
    Route::post('/gestoes/{gestao}/inatividade', [DeclaracaoInatividadeController::class, 'salvar']);
    Route::post('/gestoes/{gestao}/inatividade/aprovar', [DeclaracaoInatividadeController::class, 'aprovar']);
    Route::get('/gestoes/{gestao}/movimentacoes', [FinanceiroController::class, 'movimentacoes']);
    Route::post('/gestoes/{gestao}/movimentacoes', [FinanceiroController::class, 'criarMovimentacao']);
    Route::post('/movimentacoes/{movimentacao}/confirmar', [FinanceiroController::class, 'confirmarMovimentacao']);
    Route::post('/movimentacoes/{movimentacao}/cancelar', [CancelamentoFinanceiroController::class, 'cancelar']);
    Route::post('/movimentacoes/{movimentacao}/retificar', [RetificacaoFinanceiraController::class, 'retificar']);
    Route::post('/gestoes/{gestao}/relatorios/fechar', [RelatorioFinanceiroController::class, 'fechar']);
    Route::post('/gestoes/{gestao}/relatorios', [RelatorioFinanceiroController::class, 'publicar']);
    Route::get('/movimentacoes/{movimentacao}/comprovantes', [ComprovanteFinanceiroController::class, 'index']);
    Route::post('/movimentacoes/{movimentacao}/comprovantes', [ComprovanteFinanceiroController::class, 'store']);
    Route::get('/comprovantes/{id}/download', [ComprovanteFinanceiroController::class, 'download']);
});

// Só administrador de verdade pode gerenciar contas de usuário e ver logs
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/usuarios', [AdminUserController::class, 'index']);
    Route::post('/admin/usuarios', [AdminUserController::class, 'store']);
    Route::put('/admin/usuarios/{id}/role', [AdminUserController::class, 'updateRole']);
    Route::put('/admin/usuarios/{id}/senha', [AdminUserController::class, 'updatePassword']);
    Route::delete('/admin/usuarios/{id}', [AdminUserController::class, 'destroy']);

    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/logs/acoes', [LogController::class, 'acoesDisponiveis']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    NoticiaController, ProjetoController, CardapioController, OuvinteController,
    TransparenciaController, GabaritoController, HorarioController, TurmaController,
    DiretoriaController, InicioMediaController, AuthController
};

// Rotas públicas de autenticação — throttle aqui, que é onde a requisição
// de fato chega (sem sessão). O grupo 'auth:sanctum' abaixo era dead code:
// exigia estar autenticado para acessar login/register, o que nunca acontece.
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/noticias', [NoticiaController::class, 'index']);
Route::get('/noticias/{id}', [NoticiaController::class, 'show']);
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
Route::get('/transparencia/{id}', [TransparenciaController::class, 'show']);
Route::post('/ouvintes', [OuvinteController::class, 'store']);

// GET /user: usado por checkSession() no frontend para validar a sessão
// junto ao backend. Precisa estar autenticado, mas não precisa ser admin.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/noticias', [NoticiaController::class, 'store']);
    Route::put('/noticias/{id}', [NoticiaController::class, 'update']);
    Route::delete('/noticias/{id}', [NoticiaController::class, 'destroy']);

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
    Route::delete('/diretorias/{id}', [DiretoriaController::class, 'destroy']);

    Route::post('/inicio-media', [InicioMediaController::class, 'store']);

    Route::get('/ouvintes', [OuvinteController::class, 'index']);
    Route::get('/ouvintes/{id}', [OuvinteController::class, 'show']);
    Route::put('/ouvintes/{id}', [OuvinteController::class, 'update']);
    Route::delete('/ouvintes/{id}', [OuvinteController::class, 'destroy']);
});
?>
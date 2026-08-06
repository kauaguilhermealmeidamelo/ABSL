<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    NoticiaController,
    ProjetoController,
    CardapioController,
    OuvinteController,
    TransparenciaController,
    GabaritoController,
    HorarioController,
    AuthController
};

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rotas públicas de leitura
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

Route::get('/transparencia', [TransparenciaController::class, 'index']);
Route::get('/transparencia/{id}', [TransparenciaController::class, 'show']);

// Rota pública para ouvintes (submissão anônima ou identificada)
Route::post('/ouvintes', [OuvinteController::class, 'store']);
Route::get('/ouvintes/{id}', [OuvinteController::class, 'show']);

// Rotas protegidas (admin)
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Verificar admin
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // CRUD de Notícias (apenas admin)
    Route::post('/noticias', [NoticiaController::class, 'store']);
    Route::put('/noticias/{id}', [NoticiaController::class, 'update']);
    Route::delete('/noticias/{id}', [NoticiaController::class, 'destroy']);

    // CRUD de Projetos (apenas admin)
    Route::post('/projetos', [ProjetoController::class, 'store']);
    Route::put('/projetos/{id}', [ProjetoController::class, 'update']);
    Route::delete('/projetos/{id}', [ProjetoController::class, 'destroy']);

    // CRUD de Cardápio (apenas admin)
    Route::post('/cardapio', [CardapioController::class, 'store']);
    Route::put('/cardapio/{id}', [CardapioController::class, 'update']);
    Route::delete('/cardapio/{id}', [CardapioController::class, 'destroy']);

    // CRUD de Gabarito (apenas admin)
    Route::post('/gabarito', [GabaritoController::class, 'store']);
    Route::put('/gabarito/{id}', [GabaritoController::class, 'update']);
    Route::delete('/gabarito/{id}', [GabaritoController::class, 'destroy']);

    // CRUD de Transparência (apenas admin)
    Route::post('/transparencia', [TransparenciaController::class, 'store']);
    Route::put('/transparencia/{id}', [TransparenciaController::class, 'update']);
    Route::delete('/transparencia/{id}', [TransparenciaController::class, 'destroy']);

    // CRUD de Horário (apenas admin)
    Route::post('/horario', [HorarioController::class, 'store']);
    Route::put('/horario/{id}', [HorarioController::class, 'update']);
    Route::delete('/horario/{id}', [HorarioController::class, 'destroy']);

    // Gerenciar Ouvintes (admin)
    Route::get('/ouvintes', [OuvinteController::class, 'index']);
    Route::put('/ouvintes/{id}', [OuvinteController::class, 'update']);
    Route::delete('/ouvintes/{id}', [OuvinteController::class, 'destroy']);
});
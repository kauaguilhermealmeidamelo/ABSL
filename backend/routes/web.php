<?php

use Illuminate\Support\Facades\Route;

// O frontend é uma SPA Vue separada (pasta /frontend). O fluxo de deploy é:
//   1. cd frontend && npm run build
//   2. copiar o conteúdo de frontend/dist/* para backend/public/
//
// O .htaccess do backend já serve arquivos estáticos existentes em public/
// diretamente (sem passar por aqui) quando rodando via Apache. Esta rota
// catch-all cobre o caso de "php artisan serve", servindo o index.html
// copiado do build do Vue como fallback de SPA.
Route::get('/{any}', function () {
    $indexPath = public_path('index.html');

    if (file_exists($indexPath)) {
        return response()->file($indexPath);
    }

    return response(
        'Build do frontend não encontrado em public/index.html. '.
        'Rode "npm run build" dentro de /frontend e copie frontend/dist/* '.
        'para backend/public/ antes de acessar esta rota.',
        200
    );
})->where('any', '.*')->name('app');
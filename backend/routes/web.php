<?php

use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', function () {
    $baseUrl = rtrim(config('app.url'), '/');

    return response(
        "User-agent: *\n".
        "Allow: /\n".
        "Disallow: /api/\n".
        "Disallow: /usuarios\n".
        "Disallow: /admin/\n".
        "Sitemap: {$baseUrl}/sitemap.xml\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
})->name('robots');

Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(config('app.url'), '/');
    $paths = [
        '/inicio',
        '/horario',
        '/noticias',
        '/projetos',
        '/gabarito',
        '/transparencia',
        '/cardapio',
        '/ouvintes',
        '/mapa',
    ];

    $urls = collect($paths)->map(function (string $path) use ($baseUrl) {
        return "  <url><loc>{$baseUrl}{$path}</loc></url>";
    })->implode("\n");

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
        "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n".
        $urls."\n".
        "</urlset>";

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

// O frontend é uma SPA Vue separada (pasta /frontend). O fluxo de deploy é:
//   1. cd frontend && npm run build
//   2. copiar o conteúdo de frontend/dist/* para backend/public/
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
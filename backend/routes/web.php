<?php

use App\Http\Controllers\SitemapController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

// O frontend é uma SPA Vue separada (pasta /frontend). O fluxo de deploy é:
//   1. cd frontend && npm run build
//   2. copiar o conteúdo de frontend/dist/* para backend/public/
//
// O .htaccess do backend já serve arquivos estáticos existentes em public/
// diretamente (sem passar por aqui) quando rodando via Apache. Esta rota
// catch-all cobre o caso de "php artisan serve", servindo o index.html
// copiado do build do Vue como fallback de SPA.

// SEO: sitemap dinâmico gerado pelo Laravel. Deve ficar antes do catch-all
// para que /sitemap.xml não seja entregue como a SPA Vue.
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Verificação de e-mail (links assinados enviados pelo VerifyEmail).
// O FRONTEND_URL deve apontar para a SPA (ex: http://localhost:5173) para
// que o usuário volte ao site após clicar no link do e-mail.
Route::get('/email/verify/{id}/{hash}', function (int $id, string $hash) {
    // A URL de verificação é assinada e contém o ID/hash do usuário.
    // Não exigimos sessão aqui para que o link funcione ao abrir o e-mail
    // em outro dispositivo ou navegador.
    $user = User::findOrFail($id);

    abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

    if (! $user->hasVerifiedEmail()) {
        $user->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($user));
    }

    return redirect()->away(
        rtrim(config('app.frontend_url'), '/').'/conta?verificado=1'
    );
})->middleware('signed')->name('verification.verify');

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

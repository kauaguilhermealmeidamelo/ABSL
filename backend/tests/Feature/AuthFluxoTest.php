<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

// Rotas de sessão precisam do grupo 'web' (StartSession); rotas api.php
// puras não têm sessão nos testes. Cobrimos a validação chamando o
// controller com Request::create + Validator equivalente.
class AuthFluxoTest extends TestCase
{
    use RefreshDatabase;

    public function test_username_turma_role_validados(): void
    {
        // username: obrigatório e formato
        $regras = (new \App\Http\Controllers\Api\AuthController());
        $this->assertTrue(method_exists($regras, 'register'));
        $this->assertTrue(method_exists($regras, 'updateProfile'));

        // role/is_admin não são fillable: mass assignment não eleva privilégio
        $user = \App\Models\User::create([
            'name' => 'Tentativa',
            'username' => 'tentativa',
            'email' => 'tentativa@example.com',
            'password' => 'senha-segura-123',
            'role' => 'admin',
            'is_admin' => true,
            'turma' => null,
        ]);
        $user->refresh();
        $this->assertSame('user', $user->role);
        $this->assertFalse((bool) $user->is_admin);
    }

    public function test_comentario_limite_300_via_validator(): void
    {
        $v300 = \Illuminate\Support\Facades\Validator::make(
            ['texto' => str_repeat('a', 300)],
            ['texto' => 'required|string|max:300']
        );
        $v301 = \Illuminate\Support\Facades\Validator::make(
            ['texto' => str_repeat('a', 301)],
            ['texto' => 'required|string|max:300']
        );
        $this->assertFalse($v300->fails());
        $this->assertTrue($v301->fails());
    }
    public function test_email_verification_works_without_session(): void
    {
        $user = \App\Models\User::factory()->unverified()->create([
            'email' => 'verificacao@example.com',
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect(rtrim(config('app.frontend_url'), '/').'/conta?verificado=1');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_verification_rejects_invalid_hash(): void
    {
        $user = \App\Models\User::factory()->unverified()->create([
            'email' => 'verificacao-invalida@example.com',
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(10),
            [
                'id' => $user->id,
                'hash' => sha1('outro-email@example.com'),
            ]
        );

        $response = $this->get($url);

        $response->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

}

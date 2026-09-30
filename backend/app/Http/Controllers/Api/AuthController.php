<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Auditoria;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        // Auth::attempt($credentials, $remember): com remember=true o Laravel
        // emite o cookie remember_token (persistente); sem ele, só a sessão
        // normal sujeita a SESSION_LIFETIME. Nunca habilitado por padrão.
        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais informadas não conferem.'],
            ]);
        }

        $request->session()->regenerate();

        Auditoria::registrar('login', descricao: "Login de {$request->user()->email}");

        return response()->json(['user' => $request->user()]);
    }

    public function register(Request $request)
    {
        // Cadastro público: role é SEMPRE 'user' (ignora qualquer valor
        // enviado pelo frontend). Turma é opcional, mas se enviada precisa
        // existir na tabela turmas (única fonte de verdade).
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required', 'string', 'max:30', 'unique:users,username',
                'regex:/^[a-zA-Z0-9._]+$/',
            ],
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'turma' => ['nullable', 'string', 'max:10', Rule::exists('turmas', 'codigo')],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => mb_strtolower(ltrim(trim($data['username']), '@')),
            'email' => $data['email'],
            'password' => $data['password'],
            'turma' => $data['turma'] ?? null,
            // role/is_admin NÃO vêm do request: defaults do banco ('user'/false).
        ]);

        // E-mail de verificação oficial do Laravel (MustVerifyEmail).
        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        Auditoria::registrar(
            'cadastro',
            descricao: "Cadastro de {$user->email} (@{$user->username})",
            entidade: 'usuario',
            entidadeId: $user->id
        );

        return response()->json(['user' => $user->fresh()], 201);
    }

    public function logout(Request $request)
    {
        $usuario = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auditoria::registrar(
            'logout',
            descricao: $usuario ? "Logout de {$usuario->email}" : null,
            user: $usuario
        );

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Perfil do usuário autenticado (área "Minha conta").
     * Nunca permite alterar role/is_admin; turma validada contra turmas.
     * Troca de e-mail reseta a verificação e reenvia o e-mail oficial.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required', 'string', 'max:30',
                Rule::unique('users', 'username')->ignore($user->id),
                'regex:/^[a-zA-Z0-9._]+$/',
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // null ou '' = "Não sou aluno".
            'turma' => ['nullable', 'string', 'max:10', Rule::exists('turmas', 'codigo')],
        ]);

        $emailMudou = mb_strtolower($data['email']) !== mb_strtolower($user->email);

        $user->name = $data['name'];
        $user->username = mb_strtolower(ltrim(trim($data['username']), '@'));
        $user->email = $data['email'];
        $turma = $data['turma'] ?? null;
        $user->turma = ($turma === '' ? null : $turma);

        if ($emailMudou) {
            $user->email_verified_at = null;
            $user->save();
            event(new Registered($user));
        } else {
            $user->save();
        }

        Auditoria::registrar(
            'atualizou_perfil',
            descricao: "Atualizou o perfil de {$user->email}",
            entidade: 'usuario',
            entidadeId: $user->id
        );

        return response()->json([
            'user' => $user->fresh(),
            'email_verification_required' => $emailMudou,
        ]);
    }

    /**
     * Reenvia o e-mail de verificação (com throttle na rota).
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json(['message' => 'E-mail de verificação reenviado.']);
    }
}
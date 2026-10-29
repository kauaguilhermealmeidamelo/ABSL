<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Auditoria;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Envia o link de redefinição. A resposta é SEMPRE a mesma, exista ou
     * não uma conta com esse e-mail, para não revelar quem está cadastrado.
     */
    public function forgot(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        Password::sendResetLink(['email' => $data['email']]);

        return response()->json([
            'message' => 'Se o e-mail estiver cadastrado, você receberá um link para redefinir a senha.',
        ]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password, // cast 'hashed' do model
                    'remember_token' => Str::random(60), // invalida "lembrar de mim"
                ])->save();

                event(new PasswordReset($user));

                Auditoria::registrar(
                    'redefiniu_senha',
                    descricao: "Redefiniu a senha de {$user->email} pelo link de recuperação",
                    entidade: 'usuario',
                    entidadeId: $user->id,
                    user: $user
                );
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => ['Link inválido ou expirado. Peça um novo link de redefinição.'],
            ]);
        }

        return response()->json(['message' => 'Senha redefinida com sucesso. Você já pode entrar.']);
    }
}

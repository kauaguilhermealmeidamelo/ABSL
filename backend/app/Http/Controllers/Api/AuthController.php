<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['user' => $user], 201);
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
}
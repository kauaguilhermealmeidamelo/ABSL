<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return User::select('id', 'name', 'email', 'role', 'is_admin', 'turma', 'created_at')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $data['is_admin'] = $data['is_admin'] ?? false;
        $data['role'] = $data['is_admin'] ? 'admin' : 'user';

        // 'password' já é hasheada automaticamente pelo cast 'hashed' em User::casts().
        $user = User::create($data);

        return response()->json($user->only(['id', 'name', 'email', 'role', 'is_admin', 'turma', 'created_at']), 201);
    }

    public function updatePassword(Request $request, string $id)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => $data['password']]);

        return response()->json(['message' => 'Senha atualizada com sucesso.']);
    }

    public function destroy(Request $request, string $id)
    {
        if ((int) $id === $request->user()->id) {
            abort(422, 'Você não pode excluir sua própria conta.');
        }

        $user = User::findOrFail($id);

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            abort(422, 'Não é possível excluir o único administrador do sistema.');
        }

        $user->delete();

        return response()->noContent();
    }
}
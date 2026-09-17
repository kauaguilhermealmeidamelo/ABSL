<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Auditoria;
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
            'role' => 'required|in:admin,imprensa,user',
        ]);

        $data['is_admin'] = $data['role'] === 'admin';

        $user = User::create($data);

        Auditoria::registrar(
            'criou_usuario',
            descricao: "Criou o usuário {$user->email} ({$user->role})",
            entidade: 'usuario',
            entidadeId: $user->id
        );

        return response()->json($user->only(['id', 'name', 'email', 'role', 'is_admin', 'turma', 'created_at']), 201);
    }

    public function updatePassword(Request $request, string $id)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => $data['password']]);

        Auditoria::registrar(
            'atualizou_senha_usuario',
            descricao: "Atualizou a senha de {$user->email}",
            entidade: 'usuario',
            entidadeId: $user->id
        );

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

        $emailExcluido = $user->email;
        $user->delete();

        Auditoria::registrar(
            'excluiu_usuario',
            descricao: "Excluiu o usuário {$emailExcluido}",
            entidade: 'usuario',
            entidadeId: (int) $id
        );

        return response()->noContent();
    }
}
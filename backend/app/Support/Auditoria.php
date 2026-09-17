<?php

namespace App\Support;

use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * Helper central de auditoria. Alimenta a tela de Logs (visível apenas ao
 * administrador máximo — role 'admin', ver EnsureUserIsAdmin).
 *
 * Uso:
 *   Auditoria::registrar('login', descricao: "Login de {$user->email}");
 *   Auditoria::registrar('excluiu_noticia', entidade: 'noticia', entidadeId: $id);
 *
 * Ao criar/editar novos controllers de gerenciamento, adicione uma chamada
 * de Auditoria::registrar() nas ações de criar/editar/excluir, seguindo o
 * mesmo padrão já aplicado em AuthController e AdminUserController.
 */
class Auditoria
{
    public static function registrar(
        string $acao,
        ?string $descricao = null,
        ?string $entidade = null,
        int|string|null $entidadeId = null,
        ?User $user = null
    ): void {
        $usuario = $user ?? RequestFacade::user();

        Log::create([
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'descricao' => $descricao,
            'user_id' => $usuario?->id,
            'user_nome' => $usuario?->name,
            'ip' => RequestFacade::ip(),
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Lista paginada de logs de auditoria. A rota é protegida pelo
     * middleware 'admin' (routes/api.php) — diferente das demais rotas de
     * gerenciamento, que também aceitam 'imprensa'. Aqui só o
     * administrador máximo tem acesso.
     */
    public function index(Request $request)
    {
        $request->validate([
            'data' => 'nullable|date',
        ]);

        $query = Log::orderBy('created_at', 'desc');

        if ($acao = $request->query('acao')) {
            $query->where('acao', $acao);
        }

        if ($busca = $request->query('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('descricao', 'like', "%{$busca}%")
                    ->orWhere('user_nome', 'like', "%{$busca}%")
                    ->orWhere('acao', 'like', "%{$busca}%");
            });
        }

        // Filtro por dia específico: recebe 'yyyy-mm-dd' (formato do
        // <input type="date">) e filtra por whereDate, comparando apenas a
        // parte de data de 'created_at' — sem se importar com a hora.
        if ($data = $request->query('data')) {
            $query->whereDate('created_at', $data);
        }

        return $query->paginate(30)->withQueryString();
    }

    /**
     * Lista as ações distintas já registradas, para popular o filtro por
     * tipo de ação no frontend sem precisar hardcodar a lista lá.
     */
    public function acoesDisponiveis()
    {
        return Log::select('acao')->distinct()->orderBy('acao')->pluck('acao');
    }
}
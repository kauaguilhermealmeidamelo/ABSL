<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visita;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    /**
     * Registra uma visita. Rota pública, chamada pelo frontend no boot do app.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'path' => 'nullable|string|max:255',
        ]);

        Visita::create([
            'ip' => $request->ip(),
            'path' => $data['path'] ?? null,
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->noContent();
    }

    /**
     * Estatísticas para o dashboard administrativo. Protegida por admin.
     */
    public function estatisticas()
    {
        $hoje = now()->startOfDay();
        $semana = now()->startOfWeek();
        $mes = now()->startOfMonth();

        $ultimos7Dias = Visita::selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return response()->json([
            'total' => Visita::count(),
            'hoje' => Visita::where('created_at', '>=', $hoje)->count(),
            'semana' => Visita::where('created_at', '>=', $semana)->count(),
            'mes' => Visita::where('created_at', '>=', $mes)->count(),
            'visitantes_unicos_hoje' => Visita::where('created_at', '>=', $hoje)->distinct('ip')->count('ip'),
            'ultimos_7_dias' => $ultimos7Dias,
        ]);
    }
}
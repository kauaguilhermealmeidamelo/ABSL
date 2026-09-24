<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Models\NoticiaCurtida;
use Illuminate\Http\Request;

class NoticiaCurtidaController extends Controller
{
    public function toggle(Request $request, string $id)
    {
        $noticia = Noticia::findOrFail($id);
        $userId = $request->user()->id;

        $curtida = NoticiaCurtida::where('noticia_id', $noticia->id)
            ->where('user_id', $userId)
            ->first();

        if ($curtida) {
            $curtida->delete();
            $curtido = false;
        } else {
            try {
                NoticiaCurtida::create(['noticia_id' => $noticia->id, 'user_id' => $userId]);
            } catch (\Illuminate\Database\QueryException) {
                // Índice único disparou: outra requisição já curtiu no mesmo
                // instante. O resultado desejado (curtido = true) já existe.
            }
            $curtido = true;
        }

        return response()->json([
            'curtido' => $curtido,
            'curtidas_count' => $noticia->curtidas()->count(),
        ]);
    }
}
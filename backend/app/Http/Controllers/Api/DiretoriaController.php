<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diretoria;
use Illuminate\Http\Request;

class DiretoriaController extends Controller
{
    public function index()
    {
        return Diretoria::where('ativo', true)->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'members' => 'nullable|array',
        ]);

        $data['criado_por'] = $request->user() ? $request->user()->id : null;

        return response()->json(Diretoria::create($data), 201);
    }

    public function update(Request $request, string $id)
    {
        $dir = Diretoria::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'ativo' => 'boolean',
        ]);
        $dir->update($data);
        return $dir;
    }

    public function destroy(string $id)
    {
        Diretoria::findOrFail($id)->delete();
        return response()->noContent();
    }
}

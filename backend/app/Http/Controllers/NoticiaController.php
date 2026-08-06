<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index()
    {
        return Noticia::where('ativo', true)
            ->orderBy('data_publicacao', 'desc')
            ->get();
    }

    public function show($id)
    {
        return Noticia::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->authorize('admin'); // Middleware de autorização

        return Noticia::create($request->validated());
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin');

        $noticia = Noticia::findOrFail($id);
        $noticia->update($request->validated());
        
        return $noticia;
    }

    public function destroy($id)
    {
        $this->authorize('admin');

        Noticia::findOrFail($id)->delete();
        
        return response()->noContent();
    }
}
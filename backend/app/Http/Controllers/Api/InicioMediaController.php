<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InicioMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InicioMediaController extends Controller
{
    public function index()
    {
        return InicioMedia::where('ativo', true)->orderBy('created_at', 'desc')->first();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/*',
        ]);

        $file = $request->file('file');
        $path = $file->store('public/inicio_media');
        $url = Storage::url($path);

        // mark previous as inactive
        InicioMedia::where('ativo', true)->update(['ativo' => false]);

        $media = InicioMedia::create([
            'file_name' => $file->getClientOriginalName(),
            'url' => $url,
            'criado_por' => $request->user() ? $request->user()->id : null,
            'ativo' => true,
        ]);

        return response()->json($media, 201);
    }
}

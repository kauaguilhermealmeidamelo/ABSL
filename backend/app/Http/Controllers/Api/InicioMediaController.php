<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InicioMedia;
use App\Support\Auditoria;
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

        $anteriores = InicioMedia::where('ativo', true)->get();
        foreach ($anteriores as $antigo) {
            $this->deleteArquivo($antigo->url);
        }
        InicioMedia::where('ativo', true)->update(['ativo' => false]);

        $media = InicioMedia::create([
            'file_name' => $file->getClientOriginalName(),
            'url' => $url,
            'criado_por' => $request->user() ? $request->user()->id : null,
            'ativo' => true,
        ]);

        Auditoria::registrar(
            'atualizou_video_inicio',
            descricao: "Enviou novo vídeo da tela inicial: {$media->file_name}",
            entidade: 'inicio_media',
            entidadeId: $media->id
        );

        return response()->json($media, 201);
    }

    private function deleteArquivo(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = preg_replace('#^.*/storage/#', '', $url);

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
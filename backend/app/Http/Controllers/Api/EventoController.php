<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventoResource;
use App\Models\Evento;
use App\Support\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    public function index()
    {
        $rows = Cache::remember('eventos.index', 300, function () {
            return Evento::where('ativo', true)
                ->orderBy('data_evento', 'asc')
                ->get()
                ->toArray();
        });

        return EventoResource::collection(Evento::hydrate($rows));
    }

    public function show(string $id)
    {
        return new EventoResource(Evento::where('ativo', true)->findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_evento' => 'required|date',
            // Só http(s): evita javascript: e afins no botão "Comprar ingresso".
            'link_ingresso' => ['nullable', 'url:http,https', 'max:500'],
            'preco' => 'nullable|numeric|min:0|max:99999.99',
            'gratuito' => 'nullable|boolean',
            'imagem' => 'nullable|image|max:5120',
        ]);

        $dados = collect($validated)->except('imagem')->all();
        $dados['gratuito'] = (bool) ($dados['gratuito'] ?? false);
        if ($dados['gratuito']) {
            $dados['preco'] = null; // gratuito não tem preço
        }

        $evento = new Evento($dados);
        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('eventos', 'public');
            $evento->imagem_url = Storage::disk('public')->url($path);
        }
        $evento->autor_id = $request->user()->id;
        $evento->save();
        Cache::forget('eventos.index');

        Auditoria::registrar(
            'criou_evento',
            descricao: "Criou o evento \"{$evento->titulo}\"",
            entidade: 'evento',
            entidadeId: $evento->id
        );

        return new EventoResource($evento);
    }

    public function update(Request $request, string $id)
    {
        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_evento' => 'sometimes|required|date',
            'link_ingresso' => ['nullable', 'url:http,https', 'max:500'],
            'preco' => 'nullable|numeric|min:0|max:99999.99',
            'gratuito' => 'nullable|boolean',
            'imagem' => 'nullable|image|max:5120',
            'remover_imagem' => 'nullable|boolean',
        ]);

        $dados = collect($validated)->except(['imagem', 'remover_imagem'])->all();
        if (array_key_exists('gratuito', $dados)) {
            $dados['gratuito'] = (bool) $dados['gratuito'];
            if ($dados['gratuito']) {
                $dados['preco'] = null;
            }
        }
        $evento->fill($dados);

        if ($request->hasFile('imagem')) {
            $this->deleteArquivo($evento->imagem_url);
            $path = $request->file('imagem')->store('eventos', 'public');
            $evento->imagem_url = Storage::disk('public')->url($path);
        } elseif ($request->boolean('remover_imagem')) {
            $this->deleteArquivo($evento->imagem_url);
            $evento->imagem_url = null;
        }

        $evento->save();
        Cache::forget('eventos.index');

        Auditoria::registrar(
            'editou_evento',
            descricao: "Editou o evento \"{$evento->titulo}\"",
            entidade: 'evento',
            entidadeId: $evento->id
        );

        return new EventoResource($evento);
    }

    public function destroy(string $id)
    {
        $evento = Evento::findOrFail($id);
        $titulo = $evento->titulo;

        $this->deleteArquivo($evento->imagem_url);
        $evento->delete();
        Cache::forget('eventos.index');

        Auditoria::registrar(
            'excluiu_evento',
            descricao: "Excluiu o evento \"{$titulo}\"",
            entidade: 'evento',
            entidadeId: (int) $id
        );

        return response()->noContent();
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

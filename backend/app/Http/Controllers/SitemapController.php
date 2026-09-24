<?php

namespace App\Http\Controllers;

use App\Models\Gabarito;
use App\Models\Noticia;
use App\Models\Projeto;
use App\Models\Transparencia;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $urls = [
            ['loc' => $baseUrl . '/', 'lastmod' => null],
            ['loc' => $baseUrl . '/inicio', 'lastmod' => null],
            ['loc' => $baseUrl . '/horario', 'lastmod' => null],
            ['loc' => $baseUrl . '/noticias', 'lastmod' => null],
            ['loc' => $baseUrl . '/projetos', 'lastmod' => null],
            ['loc' => $baseUrl . '/gabarito', 'lastmod' => null],
            ['loc' => $baseUrl . '/transparencia', 'lastmod' => null],
            ['loc' => $baseUrl . '/cardapio', 'lastmod' => null],
            ['loc' => $baseUrl . '/ouvintes', 'lastmod' => null],
            ['loc' => $baseUrl . '/mapa', 'lastmod' => null],
        ];

        Noticia::where('ativo', true)
            ->orderByDesc('id')
            ->get(['id', 'updated_at'])
            ->each(function (Noticia $noticia) use (&$urls, $baseUrl) {
                $urls[] = [
                    'loc' => $baseUrl . '/noticias/' . $noticia->id,
                    'lastmod' => $noticia->updated_at?->toAtomString(),
                ];
            });

        Projeto::query()
            ->orderByDesc('id')
            ->get(['id', 'updated_at'])
            ->each(function (Projeto $projeto) use (&$urls, $baseUrl) {
                $urls[] = [
                    'loc' => $baseUrl . '/projetos/' . $projeto->id,
                    'lastmod' => $projeto->updated_at?->toAtomString(),
                ];
            });

        Gabarito::where('ativo', true)
            ->orderByDesc('id')
            ->get(['id', 'updated_at'])
            ->each(function (Gabarito $gabarito) use (&$urls, $baseUrl) {
                $urls[] = [
                    'loc' => $baseUrl . '/gabarito/' . $gabarito->id,
                    'lastmod' => $gabarito->updated_at?->toAtomString(),
                ];
            });

        Transparencia::where('ativo', true)
            ->orderByDesc('id')
            ->get(['id', 'updated_at'])
            ->each(function (Transparencia $documento) use (&$urls, $baseUrl) {
                $urls[] = [
                    'loc' => $baseUrl . '/transparencia/' . $documento->id,
                    'lastmod' => $documento->updated_at?->toAtomString(),
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";

            if ($url['lastmod']) {
                $xml .= '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</lastmod>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}

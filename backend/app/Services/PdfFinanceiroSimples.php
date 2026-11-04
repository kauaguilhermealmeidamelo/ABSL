<?php

namespace App\Services;

class PdfFinanceiroSimples
{
    public function gerar(array $linhas): string
    {
        $normalizadas = [];
        foreach ($linhas as $linha) {
            foreach (explode("\n", wordwrap((string) $linha, 90, "\n", true)) as $parte) {
                $normalizadas[] = $parte;
            }
        }
        $paginas = array_chunk($normalizadas, 48) ?: [[]];
        $objetos = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];
        $ids = [];
        $proximo = 4;
        foreach ($paginas as $linhasPagina) {
            $pagina = $proximo++;
            $fluxoId = $proximo++;
            $ids[] = $pagina;
            $fluxo = "BT /F1 10 Tf 40 790 Td 14 TL\n";
            foreach ($linhasPagina as $linha) {
                $texto = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $linha);
                $texto = preg_replace('/[\x00-\x1F]/', ' ', $texto ?: '');
                $texto = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texto);
                $fluxo .= "({$texto}) Tj T*\n";
            }
            $fluxo .= "ET\n";
            $objetos[$pagina] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents {$fluxoId} 0 R >>";
            $objetos[$fluxoId] = "<< /Length ".strlen($fluxo)." >>\nstream\n{$fluxo}endstream";
        }
        $filhos = implode(' ', array_map(fn ($id) => "{$id} 0 R", $ids));
        $objetos[2] = "<< /Type /Pages /Kids [{$filhos}] /Count ".count($ids)." >>";
        ksort($objetos);
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objetos as $id => $objeto) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$objeto}\nendobj\n";
        }
        $inicio = strlen($pdf);
        $tamanho = max(array_keys($objetos)) + 1;
        $pdf .= "xref\n0 {$tamanho}\n0000000000 65535 f \n";
        for ($id = 1; $id < $tamanho; $id++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id])."\n";
        }
        return $pdf."trailer\n<< /Size {$tamanho} /Root 1 0 R >>\nstartxref\n{$inicio}\n%%EOF";
    }
}

<?php

namespace App\Services;

class PdfFinanceiroSimples
{
    public function gerar(array $linhas): string
    {
        $conteudo = "BT /F1 10 Tf 40 790 Td 14 TL\n";
        foreach (array_slice($linhas, 0, 45) as $linha) {
            $texto = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', (string) $linha);
            $texto = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texto ?: '');
            $conteudo .= "({$texto}) Tj T*\n";
        }
        $conteudo .= "ET\n";
        $objetos = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [4 0 R] /Count 1 >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents 5 0 R >>',
            5 => "<< /Length ".strlen($conteudo)." >>\nstream\n{$conteudo}endstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objetos as $id => $objeto) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$objeto}\nendobj\n";
        }
        $inicio = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i])."\n";
        }
        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$inicio}\n%%EOF";
    }
}

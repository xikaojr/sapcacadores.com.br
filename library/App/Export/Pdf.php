<?php

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '240');


require_once realpath(dirname(__FILE__)) . '/../../Classes/mpdf/mpdf.php';

class App_Export_Pdf {

    const A4_RETRATO = 'A4';
    const A4_PAISAGEM = 'A4-L';

    /**
     *
     * @param string $html Conteúdo a ser exportado
     * @param string $nomeArquivo Nome do arquivo gerado, sem a extensão
     * @param string $orientacao  Orientação da página, por padrão retrato(A4 retrato)
     * @param string $tipoSaida Tipo de saida, por padrao D ownload
     * @param bool $trataHtml Limpa tags html do texto
     */
    public function exportFromHtml($html, $nomeArquivo, $orientacao = self::A4_RETRATO, $tipoSaida = 'D', $trataHtml = true) {

        if ($trataHtml) {
            $html = $this->trataHtml($html);
        }

        $mpdf = new mPDF('utf-8', $orientacao);
        $mpdf->SetCreator('iTarget - Tecnologia da Informação');
        $mpdf->SetAuthor('iTarget - Tecnologia da Informação');
        $mpdf->SetLeftMargin(4);
        $mpdf->SetRightMargin(4);
        $mpdf->SetTopMargin(4);
        $mpdf->WriteHTML($html);
        $mpdf->Output("{$nomeArquivo}.pdf", $tipoSaida);
    }

    public function exportFromHtmlByEtiqueta($html, $nomeArquivo, $orientacao = self::A4_RETRATO, $tipoSaida = 'I', $leftMargin = 4, $rightMargin = 4, $topMargin = 4) {

        $html = preg_replace("/<input.*>/Uis", '', $html);
        $html = preg_replace("/<!--.*-->/Uis", '', $html);
        $html = str_replace('  ', ' ', $html);
        $html = str_replace("\n", '', $html);
        $html = stripslashes($html);

        $mpdf = new mPDF('utf-8', $orientacao, 0, '', 2, 0, 0, 0, 0, 0);
        $mpdf->SetCreator('iTarget - Tecnologia da Informação');
        $mpdf->SetAuthor('iTarget - Tecnologia da Informação');
        $mpdf->SetLeftMargin($leftMargin);
        $mpdf->SetRightMargin($rightMargin);
        $mpdf->SetTopMargin($topMargin);
        $mpdf->WriteHTML($html);
        $mpdf->Output("{$nomeArquivo}.pdf", $tipoSaida);
    }

    public function exportFromHtmlByEtiquetaEmLote($html, $nomeArquivo, $orientacao = self::A4_RETRATO, $tipoSaida = 'I', $leftMargin = 4, $rightMargin = 4, $topMargin = 4) {

        $mpdf = new mPDF('utf-8', $orientacao, 0, '', 2, 0, 0, 0, 0, 0);
        $mpdf->SetCreator('iTarget - Tecnologia da Informação');
        $mpdf->SetAuthor('iTarget - Tecnologia da Informação');
        $mpdf->SetLeftMargin($leftMargin);
        $mpdf->SetRightMargin($rightMargin);
        $mpdf->SetTopMargin($topMargin);

        foreach ($html as $h) {
            $h = preg_replace("/<input.*>/Uis", '', $h);
            $h = preg_replace("/<!--.*-->/Uis", '', $h);
            $h = str_replace('  ', ' ', $h);
            $h = str_replace("\n", '', $h);
            $h = stripslashes($h);
            $mpdf->AddPage();
            $mpdf->WriteHTML($h);
        }

        $mpdf->Output("{$nomeArquivo}.pdf", $tipoSaida);
    }

    public function exportFromHtmlByCertificado($html, $nomeArquivo, $orientacao = self::A4_RETRATO, $imagem = '', $tipoSaida = 'I') {

        $leftMargin = 4;
        $rightMargin = 4;
        $topMargin = 4;

        $html = preg_replace("/<input.*>/Uis", '', $html);
        $html = preg_replace("/<!--.*-->/Uis", '', $html);
        $html = str_replace('  ', ' ', $html);
        $html = str_replace("\n", '', $html);

        $mpdf = new mPDF('utf-8', $orientacao, 0, '', 2, 0, 0, 0, 0, 0);
        $mpdf->SetCreator('iTarget - Tecnologia da Informação');
        $mpdf->SetAuthor('iTarget - Tecnologia da Informação');
        $mpdf->SetLeftMargin($leftMargin);
        $mpdf->SetRightMargin($rightMargin);
        $mpdf->SetTopMargin($topMargin);
        $mpdf->WriteHTML("body{font-family:Arial;background:url({$imagem}) no-repeat;background-image-resolution:100dpi;background-image-resize:6;}", 1);

        $mpdf->WriteHTML($html);
        $mpdf->Output("{$nomeArquivo}.pdf", $tipoSaida);
    }

    private function trataHtml($html) {
        // remover as imagens, comentarios e inputs
        $html = preg_replace("/<img.*>/Uis", '', $html);
        $html = preg_replace("/<input.*>/Uis", '', $html);
        $html = preg_replace("/<!--.*-->/Uis", '', $html);
        $html = str_replace('  ', ' ', $html);
        $html = str_replace("\n", '', $html);
        return stripslashes($html);
    }

}

<?php

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '240');
ini_set('suhosin.post.max_vars', '2048');
ini_set('suhosin.request.max_vars', '2048');

$phpExcelPath = realpath(dirname(__FILE__)) . '/../../../Classes/PHPExcel/';

// Classe para a manipulação do arquivo
require_once $phpExcelPath . 'PHPExcel.php';
// Excel 2007
require_once $phpExcelPath . 'PHPExcel/Writer/Excel5.php';

class Sap_Export_Excel extends PHPExcel {

    private $caracteres;
    private $numeroLinha = 1;

    /**
     *
     * @param array $campos - cabecalho do relatorio, onde a chave eh o campo da tabela e valor o que ira aparecer como titulo da coluna
     * @param array $linhas - linhas da consulta
     * @param string $nomeArquivo - nome do arquivo a ser gerado
     */
    public function export($campos, $linhas, $nomeArquivo = 'relatorio') {

        $letras = array(
            ' ', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'W', 'Z');

        $this->caracteres = $letras;

        foreach ($letras as $letra):
            if (!empty($letra)):
                foreach ($letras as $letra2):
                    if (trim($letra) && trim($letra2)):
                        $this->caracteres[] = "{$letra}{$letra2}";
                    endif;
                endforeach;
            endif;
        endforeach;

        // Propriedades da pasta de trabalho
        $this->getProperties()->setCreator("Sap Cacadores");
        $this->getProperties()->setLastModifiedBy("Sap Cacadores");
        $this->getProperties()->setTitle($nomeArquivo);
        $this->getProperties()->setSubject($nomeArquivo);
        $this->getProperties()->setDescription($nomeArquivo);

        $this->setActiveSheetIndex(0);
        $this->montarCabecalho($campos);
        $this->montarCorpo($campos, $linhas);

        /** Finalizando o arquivo * */
        // Primeira linha em negrito e centralizada
        $fonteTitulo = array(
                    'font' => array(
                        'bold' => true),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        );

        $this->getActiveSheet()->setTitle('Sap Cacadores');
        $this->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($fonteTitulo);

        // Salvando o arquivo
        $objWriter = new PHPExcel_Writer_Excel5($this);
        $nomeArquivoTemp = '/tmp/' . $nomeArquivo . '_' . date('d-m-Y__') . uniqid(time()) . '.xls';
        $objWriter->save($nomeArquivoTemp);

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '_' . date('d-m-Y') . '.xls";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($nomeArquivoTemp));
        readfile($nomeArquivoTemp);
        exit();
    }

    public function montarCabecalho($campos) {
        $numeroColuna = 1;
        foreach ($campos as $campo => $valor):
            $this->getActiveSheet()->SetCellValue("{$this->caracteres[$numeroColuna]}{$this->numeroLinha}", $valor);
            $numeroColuna++;
        endforeach;
        $this->numeroLinha++;
    }

    public function montarCorpo($campos, $linhas) {
        foreach ($linhas as $linha):
            $numeroColuna = 1;
            foreach ($campos as $campo => $v):
                $valor = strip_tags($linha[$campo]);
                $this->getActiveSheet()->SetCellValue("{$this->caracteres[$numeroColuna]}{$this->numeroLinha}", $valor);
                $numeroColuna++;
            endforeach;
            $this->numeroLinha++;
        endforeach;
    }

}

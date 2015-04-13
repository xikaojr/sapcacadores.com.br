<?php

require_once realpath(dirname(__FILE__)) . '/../../Classes/HTML_TO_DOC.php';

class Itarget_Export_Word extends HTML_TO_DOC {
    
    public function export($conteudo, $nome, $download = true) {
        $this->createDoc($conteudo, $nome, $download);
    }
    
}
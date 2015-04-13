<?php

class App_Log_File implements App_Log {

    /**
     *
     * @var string diretório onde o arquivo de log será criado/editado
     */
    private $dirLogFile;

    /**
     *
     * @var string nome do arquivo de log
     */
    private $logFile;

    public function setDirLogFile($dir) {
        if (substr($dir, -1) !== '/') {
            $dir .= '/';
        }
        $this->dirLogFile = $dir;
        return $this;
    }

    public function getDirLogFile() {
        return $this->dirLogFile;
    }

    public function setLogFile($file) {
        $this->logFile = $file;
        return $this;
    }

    public function getLogFile() {
        return $this->logFile;
    }

    /**
     * Escreve no arquivo
     */
    public function write($log) {

        if (null === $this->getDirLogFile()) {
            throw new Exception('Diretório de log não definido');
        }

        if (null === $this->getLogFile()) {
            throw new Exception('Arquivo de log não definido');
        }

        if (!is_dir($this->getDirLogFile())) {
            try {
                mkdir($this->dirLogFile, 0777, true);
                chmod($this->dirLogFile, 0777);
            } catch (Exception $e) {
                throw new Exception('Impossível criar diretório de log');
            }
        }

        $file = $this->getDirLogFile() . $this->getLogFile();

        $fOpen = @fopen($file, 'a');
//        if (!$fOpen) {
//            throw new Exception('Impossível abrir arquivo de log vb');
//        }

        $conteudo = "-----------------------------------------------\n";
        $conteudo .= date("d-m-Y H:i:s") . "\n";
        $conteudo .= $log . "\n\n";

        $fWrite = @fwrite($fOpen, $conteudo);

//        if (!$fWrite) {
//            throw new Exception("Impossível abrir arquivo de log");
//        }

        unset($fOpen, $fWrite);
        return true;
    }

}
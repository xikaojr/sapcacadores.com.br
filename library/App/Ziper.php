<?php

class App_Ziper {

    private $_objZip = null;
    private $_tmpDir = '/tmp/files';
    private $_tmpDirMode = 0777;
    private $_fileName;
    private $_OrigenPathFile;

    public function __construct() {
        if ($this->_objZip == null) {
            $this->_objZip = new ZipArchive();
        }
    }

    public function setTempDir($dir) {
        $this->_tmpDir = $dir;
    }

    public function deleteZip($filename) {
        $this->_fileName = $this->_tmpDir . '/' . $filename . '.zip';
        if (file_exists($this->_fileName)) {
            unlink($this->_fileName);
        }
    }

    public function setFileName($filename) {

        $this->_fileName = $filename;

        if (!file_exists($this->_tmpDir)) {
            mkdir($this->_tmpDir, $this->_tmpDirMode);
        }

        $this->_fileName = $this->_tmpDir . '/' . $this->_fileName . '.zip';

        if ($this->_objZip->open($this->_fileName, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <{$this->_fileName}>\n");
        }
    }

    public function setOrigenPathFile($path) {
        $this->_OrigenPathFile = $path;
    }

    public function addFile($file) {
        $name = end(explode('/', $file));
        if (file_exists($file)) {
            $this->_objZip->addFile($file, $name);
        }
    }

    public function closeZip() {
        $this->_objZip->close();
    }

    public function getZipFile() {
        return $this->_fileName;
    }

}

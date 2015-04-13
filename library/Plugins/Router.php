<?php

/*
 * Muda o comportamento caso exista um arquivo no path especificado
 * Plugin responsável pela rota alternativa do sistema
 */

class Plugins_Router extends Zend_Controller_Plugin_Abstract {

    private $_controllerName;
    private $_actionName;
    private $_moduleName;
    private $_pathCliente;
    private $_validator;
    private $_view;

    /**
     * Inicializa path
     */
    public function __construct() {
        $this->_pathCliente = APPLICATION_PATH . '/modules/';
        $this->_validator = new Zend_Validate_File_Exists();
        $this->_view = Zend_Registry::get('view');
    }

    /**
     * Inicializa as variáveis principais
     */
    private function initRequest() {
        $this->_controllerName = $this->getRequest()->getControllerName();
        $this->_actionName = $this->getRequest()->getActionName();
        $this->_moduleName = $this->getRequest()->getModuleName();
    }

    /**
     * sobrescrita de metodo em Zend_Controller_Plugin_Abstract
     */
    public function preDispatch() {

        $this->initRequest();

//        if ($this->verifyControllerExists() && $this->verifyActionExists()) {
        $router = Zend_Controller_Front::getInstance();
        $router->setControllerDirectory($this->getPathController());
        $this->_view->addBasePath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/views/');
//        }
    }

    /**
     * Veritica se o controller existe
     */
    private function verifyControllerExists() {
        $this->_validator->addDirectory($this->getPathController());
        return $this->_validator->isValid($this->getNameFileControler());
    }

    /**
     * Retorna o nome do aquivo controller requerido
     */
    private function getNameFileControler() {
        $strControler = '';
        foreach (explode('-', $this->_controllerName) as $alias) {
            $strControler .= ucwords($alias);
        }
        return $strControler . 'Controller.php';
    }

    /**
     * Monta o path do controller
     */
    private function getPathController() {
        return $this->_pathCliente . '/' . $this->_moduleName . '/controllers';
    }

    /**
     * Verifica se action existe sem montar objetos
     */
    private function verifyActionExists() {
        $dispatcher = new Zend_Controller_Dispatcher_Standard();
        $actionName = $dispatcher->formatActionName($this->_actionName);

        foreach (file($this->getPathController() . '/' . $this->getNameFileControler()) as $line) {
            if (strpos($line, $actionName) !== false) {
                return true;
            }
        }
        return false;
    }

}

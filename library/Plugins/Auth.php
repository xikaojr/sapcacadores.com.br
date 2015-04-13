<?php

class Plugins_Auth extends Zend_Controller_Plugin_Abstract {

    private $_auth;
    private $_acl;
    private $_noauth = array('controller' => 'login', 'action' => 'index');
    private $_noaccess = array('controller' => 'login', 'action' => 'acesso-negado');
    private $_appSession;

    public function __construct() {
        $this->_appSession = new Zend_Session_Namespace('appSession');
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $this->_auth = Zend_Auth::getInstance();
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());
        $module = strtolower($request->getModuleName());
        $resource = $module . "_" . $controller;
        $redireciona = false;
        $publico = false;
        
        $config = Zend_Registry::get('config');
        $public = $config->public->toArray();
        
        // adiciona controllers publicos
        $publico = !self::isNeedToBeLogged($public,$module,$controller,$action);
        //var_dump($publico);
               
        if (!$this->_auth->hasIdentity() && $controller != 'login' && !$publico) {
            $controller = $this->_noauth['controller'];
            $action = $this->_noauth['action'];
            $redireciona = true;
        } else {
            //CARREGA AS PERMISSOES DE USUARIO + PERMISSOES PUBLICAS
            $id = isset($this->_auth->getIdentity()->id) ? $this->_auth->getIdentity()->id : null;
            
            $this->_acl = App_Acl::getInstance($id);
            
            if (!$this->_acl->isAllowed('default', $resource, $action) && $id != Usuarios::ADMINISTRADOR) {
                $controller = $this->_noaccess['controller'];
                $action = $this->_noaccess['action'];
                $redireciona = true;
            }
        }

        if ($redireciona) {
            // seta a action
            $request->setModuleName($module);
            $request->setControllerName($controller);
            $request->setActionName($action);
        }
    }
    
    /**
    * 
    * @param mixed $resouces 
    * @param string $module 
    * @param string $controller 
    * @param string $action 
    * @param int $startLevel 
    * @return boolean
    */
    protected function isNeedToBeLogged($resources, $module, $controller, $action, $currentLevel=0){
        
        switch ($currentLevel) {
            case 0:
                $resourcesOfLevel = (isset($resources[$module]) ? $resources[$module] : null);
                break;
            case 1:
                $resourcesOfLevel = (isset($resources[$module][$controller]) ? $resources[$module][$controller] : null);
                break;
            case 2:
                $resourcesOfLevel = (isset($resources[$module][$controller][$action]) ? $resources[$module][$controller][$action] : null);
                break;
        }
        //echo "<br/>\$currentLevel: $currentLevel: ( $module/$controller/$action)";
        if (is_array($resourcesOfLevel)) {
            if ( array_key_exists("*", $resourcesOfLevel) ) {
                return false;
            } else {
                if ($currentLevel == 0 || $currentLevel == 1) {
                    return self::isNeedToBeLogged($resources, $module, $controller, $action, ++$currentLevel);
                }
            }

        } else {
            
            if ($currentLevel == 2) {
                return ($resourcesOfLevel ? false : true);
            }
            
            return true;
        }
    }

}

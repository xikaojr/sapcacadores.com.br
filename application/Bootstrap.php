<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $_configuracao = null;

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        $view->setEncoding('UTF-8');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        Zend_Registry::set('view', $view);
    }

    protected function _initAutoLoader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Plugins');
        $autoloader->setFallbackAutoloader(true);
    }

    // Inicializando Plugins
    protected function _initPlugins() {

        $bootstrap = $this->getApplication();

        if ($bootstrap instanceof Zend_Application) {
            $bootstrap = $this;
        }

        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');

//        $front->registerPlugin(new Plugins_Auth(Zend_Auth::getInstance()));
        $front->registerPlugin(new Plugins_Layout());
//        $front->registerPlugin(new Plugins_Navigation());
//        $front->registerPlugin(new Plugins_Idioma());
//        $front->registerPlugin(new Plugins_Router());
    }

    /**
     * Inicializa a configurações global e a do cliente
     */
    protected function _initConfig() {

        $this->_configuracao = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APP_ENV);
        Zend_Registry::set('config', $this->_configuracao);
        Zend_Date::setOptions(array('format_type' => 'php'));

        $cacheDir = ROOT_PATH . 'cache/';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $backendOptions = array('cache_dir' => $cacheDir);
        $frontendOptions = array(
            'lifetime' => 7200, // duas horas de vida
            'automatic_serialization' => true // guardar de forma serializada
        );

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('cache', $cache);
    }

    /**
     * Inicia o helper
     */
    protected function _initHelpers() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->addHelperPath('App/Helper/', 'App_Helper');
    }

}

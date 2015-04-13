<?php

class Plugins_Idioma extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $language = new Zend_Session_Namespace('pais');

        $params = $request->getParams();


        if (isset($params['lang']) && !empty($params['lang'])) {
            $idiomaPadrao = ($params['lang'] == 'pt-br') ? 'pt_BR' : $params['lang'];
        } else {
            $idiomaPadrao = (isset($this->_configCliente->idioma->padrao)) ? $this->_configCliente->idioma->padrao : "pt_BR";
        }

        $idiomasPerm = array("pt_BR", "en", "es");

        if (in_array($idiomaPadrao, $idiomasPerm)) {

            if (isset($params['lang']) && !empty($params['lang'])) {
                $language->__set('idioma', $idiomaPadrao);
            } else {
                if (!$language->__isset('idioma')) {
                    $language->__set('idioma', $idiomaPadrao);
                }
            }

            $this->_translate = new Zend_Translate('gettext', '../languages/default/pt_BR.mo', 'pt_BR');
            $this->_translate->getAdapter()->addTranslation('../languages/default/es.mo', 'es');
            $this->_translate->getAdapter()->addTranslation('../languages/default/en.mo', 'en');
            $this->_translate->getAdapter()->setLocale($language->__get('idioma'));

            Zend_Registry::set('translate', $this->_translate);
            Zend_Registry::set('idiomaApp', $language->__get('idioma'));

            Zend_Validate_Abstract::setDefaultTranslator($this->_translate);
        }
    }

}

?>

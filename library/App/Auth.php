<?php

class App_Auth extends Zend_Auth {

    public static function getInstance($namespace = 'Zend_Auth') {
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session($namespace));
        return parent::getInstance();
    }

    public static function geraHash() {
        return substr(sha1(md5(date('dmYHis') . rand(9, 99999))), 0, 49);
    }

    public static function getStorageSistema(Zend_Controller_Request_Abstract $request) {
        return App_Auth::getInstance(App_Controller_Default::SESSION_STORAGE);
    }

}

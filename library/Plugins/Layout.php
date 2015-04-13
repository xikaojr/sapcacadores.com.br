<?php

class Plugins_Layout extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $pathLayout = APPLICATION_PATH . '/modules/' . $request->getModuleName() . '/layouts';
        $customLayout = $pathLayout . "/" . $request->getModuleName() . ".phtml";
        $layout = Zend_Layout::getMvcInstance();

        if (is_file($customLayout)) {
            $layout->setLayout($request->getModuleName())->setLayoutPath($pathLayout);
        } else {
            $layout->setLayout('default')->setLayoutPath(APPLICATION_PATH . '/modules/default/layouts');
        }
    }

}

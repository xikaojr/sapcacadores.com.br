<?php

class Zend_View_Helper_UrlFor extends Zend_View_Helper_Abstract {

    public function UrlFor($urlOptions, $name = null, $reset = false, $encode = true) {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        if (is_string($urlOptions)) {
            $urlOptions = '/' . ltrim($urlOptions, '/'); // Case the first character is a '?
            $request = new Zend_Controller_Request_Http(); // Creates a cleaned instance of request http
            $request->setBaseUrl($front->getBaseUrl());
            $request->setRequestUri($urlOptions);
            $route = $router->route($request); // Return the request route with params modifieds
            $urlOptions = $route->getParams();
        }
        return $router->assemble((array) $urlOptions, $name, $reset, $encode);
    }

}

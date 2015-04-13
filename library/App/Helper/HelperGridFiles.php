<?php
class Zend_View_Helper_HelperGridFiles extends Zend_View_Helper_Abstract
{
    public function helperGridFiles()
    {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/js/default/flexigrid/css/flexigrid/flexigrid.css'));
        $this->view->headScript()->prependFile($this->view->baseUrl('/js/default/flexigrid/flexigrid.pack.js','text/javascript',array('charset' => 'utf-8')));
    }
}
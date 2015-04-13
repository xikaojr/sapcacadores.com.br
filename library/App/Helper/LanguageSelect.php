<?php header('Content-type: text/html; charset=UTF-8'); ?>
<?php

class Zend_View_Helper_LanguageSelect extends Zend_View_Helper_Abstract {

    public function languageSelect() {
        return <<<HTML
<div>
<a href="{$this->view->url(array(
                    'module' => 'default',
                    'controller' => 'utilidades',
                    'action' => 'idioma',
                    'l' => 'pt_BR',
                    'r' => rawurlencode($this->view->url())
                ))}">Português (Brasil)</a> |
<a href="{$this->view->url(array(
                    'module' => 'default',
                    'controller' => 'utilidades',
                    'action' => 'idioma',
                    'l' => 'en',
                    'r' => rawurlencode($this->view->url())
                ))}">English</a>
</div>
HTML;
    }

}

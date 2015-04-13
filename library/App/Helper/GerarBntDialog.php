<?php
class Zend_View_Helper_GerarBntDialog extends Zend_View_Helper_Abstract
{
    public function gerarBntDialog($nome,$atributos='',$mostraTagPrincipal = true)
    {
        $text = '';
        
        $text .= '
            <div class="row">
                <div class="form-actions">
                    <div class="col-md-12 botoes">
                    <button '. $atributos .' type="submit" class="btn btn-primary">'.$nome.'</button>
                    </div>
                </div>
            </div>
        ';
        
        return $text;
    }
}
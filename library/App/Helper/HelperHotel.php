<?php

/**
 * 
 */

/**
 * 
 */
class Zend_View_Helper_HelperHotel extends Zend_View_Helper_Abstract {

    /**
     * 
     * @return \Zend_View_Helper_HelperHotel
     */
    public function helperHotel() {
        return $this;
    }

    /**
     * 
     * @param array $atributo
     * @return type
     */
    public function atributo(array $atributo) {

        $retorno = array();

        foreach ($atributo as $a) {
            $retorno[] = "<img src='/images/hospedagem/atributos/{$a['sigla']}.png' alt='{$a['descricao']}' title='{$a['descricao']}' />";
        }

        return implode(' ', $retorno);
    }

    public function estrela($quantidade) {

        $retorno = '
           <div data-id="1" data-average="0" id="" style="height: 20px; width: 115px; overflow: hidden; z-index: 1; position: relative; cursor: default;">
           <div class="jRatingColor" style="width: 0px;"></div>
             <div class="jRatingAverage" style="width: ' . (23 * $quantidade ) . 'px; top: -20px;">
           </div>
           <div class="jStar" style="width: 115px; height: 20px; top: -40px; background: url(&quot;/images/hospedagem/rating/stars.png&quot;) repeat-x scroll 0% 0% transparent;"></div>
           </div>
            ';

        return $retorno;
    }

}

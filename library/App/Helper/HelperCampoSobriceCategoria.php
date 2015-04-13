<?php

class Zend_View_Helper_HelperCampoSobriceCategoria extends Zend_View_Helper_Abstract {

    public function HelperCampoSobriceCategoria($value) {
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        $valores = array();
        $valores[1] = $translate->_('Titular');
        $valores[2] = $translate->_('Associado');
        $valores[3] = $translate->_('Senior');
        $valores[4] = $translate->_('Honorario');
        $valores[5] = $translate->_('Correspondente');
        $valores[6] = $translate->_('Junior');
        $valores[7] = $translate->_('Benemerito');
        ?>

            <div class="form-group col-md-3 col-xs-12">
                <label for="categoria_sobrice" class="optional"><?php echo $translate->_('Categoria SOBRICE:'); ?></label>
                <select name="categoria_sobrice" class="form-control">
                    <option value=""><?php echo $translate->_('Selecione'); ?></option>
                    <?php foreach ($valores as $v => $c): ?>
                        <option value="<?php echo $v ?>" <?php echo ($value == $v) ? 'selected="selected"' : '' ?>><?php echo $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        <?php
    }

}

<?php

class Zend_View_Helper_HelperCampoSobriceAssociado extends Zend_View_Helper_Abstract {

    public function HelperCampoSobriceAssociado($value) {
        $translate = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        $valores = array();
        $valores['S'] = $translate->_('Sim');
        $valores['N'] = $translate->_('Nao');
        ?>

        <div class="form-group col-md-2 col-xs-12">
            <label for="associado_sobrice" class="optional"><?php echo $translate->_('Associado SOBRICE:'); ?></label>
            <select name="associado_sobrice" class="form-control">
                <option value=""><?php echo $translate->_('Selecione'); ?></option>
                <?php foreach ($valores as $v => $c): ?>
                    <option value="<?php echo $v ?>" <?php echo ($value == $v) ? 'selected="selected"' : '' ?>><?php echo $c ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

}

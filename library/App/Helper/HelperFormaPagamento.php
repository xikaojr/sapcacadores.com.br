<?php

class Zend_View_Helper_HelperFormaPagamento extends Zend_View_Helper_Abstract {

    /**
     * Retorna o html com as forma de pagamento
     * @param array $configuracao configuracao 142 para habilitar a forma de pagamento
     * @param integer $centroCustoId id do centro de custo
     */
    public function helperFormaPagamento($configuracao, $centroCustoId) {

        $translate = Zend_Registry::get('translate');
        $configuracao['valor_referencia'] = explode(',', $configuracao['valor_referencia']);
        ?>

        <div class="form-group col-md-12 col-xs-12">
            <fieldset>
                <legend><?php echo $translate->_('Forma de pagamento'); ?></legend>

                <?php if (array_intersect($configuracao['valor_referencia'], array(1))): ?>
                    <label id="forma-pagamento-boleto">
                        <input type="radio" name="forma_pagamento" value="boleto" />
                        <?php echo $translate->_('Boleto'); ?>
                        <img src="<?php echo $this->view->baseUrl("/images/icons/boleto-48px.png") ?>"/>
                    </label>
                <?php endif; ?>

                <?php if (array_intersect($configuracao['valor_referencia'], array(2))): ?>
                    <label>
                        <input type="radio" name="forma_pagamento" value="cartao" />
                        <?php echo $translate->_('Cartao de credito'); ?>
                    </label>

                    <fieldset class="many-columns" id="cartao" style="display: none;">
                        <?php echo $this->view->helperBandeirasCartoes($centroCustoId); ?>
                    </fieldset>

                <?php endif; ?>
                <?php if (array_intersect($configuracao['valor_referencia'], array(4))): ?>
                    <label>
                        <input type="radio" name="forma_pagamento" value="pagseguro" />
                        <?php echo $translate->_('Pagseguro'); ?>
                        <img src="<?php echo $this->view->baseUrl("/images/icons/pagseguro-48px.png") ?>"/>
                    </label>
                <?php endif; ?>
                <?php if (array_intersect($configuracao['valor_referencia'], array(5))): ?>
                    <label>
                        <input type="radio" name="forma_pagamento" value="paypal" />
                        <?php echo $translate->_('Paypal'); ?>
                    </label>
                <?php endif; ?>
            </fieldset>
        </div>

        <?php
    }

}

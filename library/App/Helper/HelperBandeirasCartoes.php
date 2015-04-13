<?php

class Zend_View_Helper_HelperBandeirasCartoes extends Zend_View_Helper_Abstract {

    public function helperBandeirasCartoes($centroCustoId) {
        $translate = Zend_Registry::get('translate');
        $controleLayoutCartaoTable = new ControleLayoutCartao();
        $bandeiras = $controleLayoutCartaoTable->findBandeirasByCentroCusto($centroCustoId);
        ?>
        <div class="col">
            <label>
                <?php echo $translate->_('Bandeira:'); ?>
                <select class="pequeno" name="bandeira" id="forma_pagamento-bandeira">
                    <option value=""><?php echo $translate->_('Selecione:'); ?></option>
                    <?php foreach ($bandeiras as $b): ?>
                        <option value="<?php echo $b['controle_layout_cartao_id']; ?>"><?php echo $b['bandeira_nome']; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="col margin-left-10px" id="bandeira_parcelamento">
            <label>
                <?php echo $translate->_('Forma de pagamento:'); ?>
                <select name="forma_pagamento_tipo" id="forma_pagamento-tipo" class="medio">
                    <option value=""><?php echo $translate->_('Selecione uma bandeira:'); ?></option>
                </select>
            </label>
        </div>

        <script type="text/javascript">
            $("#forma_pagamento-bandeira").change(function(){
                if($(this).val() != "") {
                    $("#forma_pagamento-tipo").html('<option value=""><?php echo $translate->_('Aguarde...'); ?></option>');
                    $.getJSON(baseUrl + "/utilidades/forma-pagamento-cartao", {centro_custo_id: <?php echo $centroCustoId; ?>, bandeira: $(this).val()}, function(data){
                        $("#forma_pagamento-tipo").html(data.html);
                    });
                } else {
                    $("#forma_pagamento-tipo").html('<option value=""><?php echo $translate->_('Selecione uma bandeira:'); ?></option>');
                }
            });
        </script>
        <?php
    }

}
?>
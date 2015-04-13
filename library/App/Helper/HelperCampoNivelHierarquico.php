<?php

class Zend_View_Helper_HelperCampoNivelHierarquico extends Zend_View_Helper_Abstract {

    public function helperCampoNivelHierarquico($value = null, $i = 1, $codigoTipo = 2) {
        $value = explode(',', $value);
        $translate = Zend_Registry::get('translate');

        if ($codigoTipo == 1) {
            $codigoTipo = 125;
        } else {
            $codigoTipo = 126;
        }

        $configuracoesTable = new Configuracoes();
        $campos = $configuracoesTable->getByCodigo($codigoTipo); // academico 125
        $opcoesNiveis = explode("\n", $campos['valor_referencia']);
        ?>
        <label class="required" for="autor-nivel_hierarquico"><?php echo $translate->_('Nivel Hierarquico:'); ?>*</label>
        <select class="form-control obrigratorio" id="autor-nivel_hierarquico_<?php echo $i ?>" name="autor[nivel_hierarquico][]">
            <option value=""><?php echo $translate->_('Selecione:'); ?></option>
            <?php $opcoes = array(); ?>
            <?php foreach ($opcoesNiveis as $opc): ?>
                <?php $opcao = explode('|', $opc); ?>
                <?php $selected = ($value[0] == trim($opcao[0])) ? 'selected="selected"' : ""; ?>
                <option <?php echo $selected ?> label="<?php echo $opcao[1] ?>" value="<?php echo trim($opcao[0]) ?>"><?php echo $opcao[1] ?></option>
                <?php $opcoes[] = trim($opcao[1]); ?>
            <?php endforeach; ?>
        </select>

        <div style="display:<?php echo ($value[0] != "99" || $value[0] == null) ? "none" : "block"; ?>;" id="campo-nivel_hierarquico_texto_<?php echo $i ?>">
            <label class="optional" for="autor-nivel_hierarquico_texto"><?php echo $translate->_('Outra'); ?></label>
            <input type="text" maxlength="60" class="form-control" value="<?php echo (isset($value[1])) ? $value[1] : null; ?>" id="autor-nivel_hierarquico_texto_<?php echo $i ?>" name="autor[nivel_hierarquico_texto][]">
        </div>
        <script type="text/javascript">
            $("#autor-nivel_hierarquico_<?php echo $i ?>").change(function() {
                var val = $(this).val();

                if (val == "99") {
                    $("#autor-nivel_hierarquico_texto_<?php echo $i ?>").val('');
                    $("#campo-nivel_hierarquico_texto_<?php echo $i ?>").show();
                } else {
                    $("#campo-nivel_hierarquico_texto_<?php echo $i ?>").hide();
                    $("#autor-nivel_hierarquico_texto_<?php echo $i ?>").val(val);
                }
            });
        </script>
        <?php
    }

}

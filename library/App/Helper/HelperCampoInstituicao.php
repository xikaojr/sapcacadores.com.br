<?php

class Zend_View_Helper_HelperCampoInstituicao extends Zend_View_Helper_Abstract {

    public function helperCampoInstituicao($descTipo, $value = null) {
        $translate = Zend_Registry::get('translate');
        $descTipo = (empty($descTipo) || !in_array($descTipo, array('EMP EVENTOS', 'FACULDADES EVENTOS'))) ? 'EMP EVENTOS' : $descTipo;
        $pessoaTable = new Pessoa();
        $campos = $pessoaTable->findByResponsavel($descTipo);
        $label = ($descTipo == 'EMP EVENTOS') ? $translate->_('Empresa:') : $translate->_('Instituicao de Ensino:');
        ?>
        <div class="col margin-left-10px">
            <dt id="pessoa-pcc_empresa_instituicao_id-label">
                <label class="required" for="pessoa-pcc_empresa_instituicao_id">
                    <?php echo $label; ?>*
                </label>
            </dt>

            <dd id="pessoa-pcc_empresa_instituicao_id-element">
                <select class="form-control obrigratorio" id="pessoa-pcc_empresa_instituicao_id" name="pessoa[pcc_empresa_instituicao_id]">
                    <option value=""><?php echo $translate->_('Selecione:'); ?></option>
                    <?php $opcoes = array(); ?>
                    <?php foreach ($campos as $opc): ?>
                        <?php $selected = ($value == $opc['id']) ? 'selected="selected"' : ''; ?>
                        <option <?php echo $selected ?> value="<?php echo $opc['id'] ?>"><?php echo $opc['nome'] ?></option>
                        <?php $opcoes[] = $opc['id']; ?>
                    <?php endforeach; ?>
                    <option value="99"><?php echo $translate->_('Outra'); ?>  / Other</option>
                </select>
            </dd>

            <div style="display:<?php echo (in_array($value, $opcoes) || $value == null) ? "none" : "block"; ?>;" id="campo-pcc_empresa_instituicao_texto">
                <dt id="pessoa-pcc_empresa_instituicao_texto-label"><label class="optional" for="pessoa-pcc_empresa_instituicao_texto"><?php echo $translate->_('Outra'); ?> / Other</label></dt>
                <dd id="pessoa-pcc_empresa_instituicao_texto-element">
                    <input type="text" maxlength="60" class="medio" value="<?php echo $value ?>" id="pessoa-pcc_empresa_instituicao_texto" name="pessoa[pcc_empresa_instituicao_texto]">
                </dd>
            </div>
        </div>

        <script type="text/javascript">
        <?php if (!in_array($value, $opcoes) && $value != null): ?>
                $("#pessoa-pcc_empresa_instituicao_id option:last").attr("selected","selected");
        <?php endif; ?>

            $("#pessoa-pcc_empresa_instituicao_id").change(function() {
                $("#pessoa-pcc_empresa_instituicao_texto").val("");
                
                if ($(this).val() == "99") {
                    $("#campo-pcc_empresa_instituicao_texto").show();
                } else {
                    $("#campo-pcc_empresa_instituicao_texto").hide();
                }
            });$("#pessoa-pcc_empresa_instituicao_id").change();
        </script>

        <?php
    }

}
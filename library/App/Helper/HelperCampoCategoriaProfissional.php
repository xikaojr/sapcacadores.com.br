<?php

class Zend_View_Helper_HelperCampoCategoriaProfissional extends Zend_View_Helper_Abstract {

    public function helperCampoCategoriaProfissional($centroCustoId, $categoriaCentroCustoId = null) {
        
        $categoria  = new CategoriaCentroCusto();
        $categorias = $categoria->getCategoriasByCentroCusto($centroCustoId, $categoriaCentroCustoId);
        $translate  = Zend_Controller_Front::getInstance()
                        ->getParam('bootstrap')->getResource('translate');
        ?>
        <?php if (count($categorias)): ?>
        <select name="associacao[categoria_profissional_id]" id="associacao-categoria_profissional_id">
            <option value=""><?php echo $translate->_('Selecione:') ?></option>
            <?php foreach($categorias as $c): ?>
                <?php $selected = ($categoriaCentroCustoId == $c['id']) ? "selected" : "" ; ?>
                <option <?php echo $selected ?> value="<?php echo $c['id'] ?>"><?php echo $c['categoria_centro_custo'] ?> (<?php echo $c['categoria_profissional'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <?php else: ?>
        <?php echo $translate->_('Nao existe categoria para o centro de custo atual') ?>
        <?php endif; ?>
        <?php
        
    }
}
?>
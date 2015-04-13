<?php

class Zend_View_Helper_HelperGetAutores extends Zend_View_Helper_Abstract {

    public function helperGetAutores(array $params) {
        $autores = array();
        $zendFilterStringToUpper = new Zend_Filter_StringToUpper();
        $trabalhoTable = new Trabalhos();

        $rsAutores = $trabalhoTable->getAutores(array('pessoa_id' => $params['pessoa_id'], 'trabalho_id' => $params['trabalho_id']));

        $autores['instituicao']['nome'] = array();
        foreach ($rsAutores as $i => $r) {

            $autores['nome'][$i] = $r['nome'];

            $indiceInstituicao = array_search($zendFilterStringToUpper->filter($r['instituicao']), $autores['instituicao']['nome']);

            if (!is_numeric($indiceInstituicao)) {
                $autores['instituicao']['nome'][$i] = $zendFilterStringToUpper->filter($r['instituicao']);
                $autores['instituicao']['pessoa'][$i] = $i + 1;
            } else {
                $autores['instituicao']['pessoa'][$i] = $indiceInstituicao + 1;
            }
        }

        foreach ($autores['nome'] as $key => $nome) :
            ?>
            <span style="text-transform: capitalize;"><?php echo $nome; ?></span><sup><?php echo $autores['instituicao']['pessoa'][$key]; ?></sup>;
            <?php endforeach; ?>
        <br />
        <?php foreach ($autores['instituicao']['nome'] as $key => $instituicao): ?>
            <?php echo $key + 1 . '. ' . $instituicao; ?>;
            <?php
        endforeach;
    }

}

<?php

class App_Form_Default_Local extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        // Permitir a geracao no estilo pessoa[campo]
        $opcoesPadrao = array('belongsTo' => 'local');

        $id = new Zend_Form_Element_Hidden("id", $opcoesPadrao);
        $this->addElement($id);

        $nome = new Zend_Form_Element_Text('descricao', $opcoesPadrao);
        $nome->setRequired(true)
                ->setLabel('Descrição')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('maxlength' => '255'))
                ->addErrorMessage('Campo requerido');
        $this->addElement($nome);
    }

}

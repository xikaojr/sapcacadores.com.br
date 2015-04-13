<?php

class App_Form_Default_Treino extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        // Permitir a geracao no estilo pessoa[campo]
        $opcoesPadrao = array('belongsTo' => 'treino');

        $localTable = new Local();
        $locais = $localTable->fetchAll();
        
        $id = new Zend_Form_Element_Hidden("id", $opcoesPadrao);
        $this->addElement($id);

        $nome = new Zend_Form_Element_Select('local_id', $opcoesPadrao);
        $nome->setRequired(true)
                ->setLabel('Local')
                ->setFilters(array('StripTags'))
                ->addErrorMessage('Campo requerido');
        $nome->addMultiOptions(array('' => 'Selecione:'));
        foreach ($locais->toArray() as $l) {
            $nome->addMultiOptions(array($l['id'] => $l['descricao']));
        }
        $this->addElement($nome);

        $nascimento = new Zend_Form_Element_Text('data', $opcoesPadrao);
        $nascimento->setRequired(true)
                ->setLabel('Data')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('class' => 'datepicker'));
        $this->addElement($nascimento);
    }

}

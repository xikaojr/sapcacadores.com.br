<?php

class App_Form_Default_Presenca extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        // Permitir a geracao no estilo pessoa[campo]
        $opcoesPadrao = array('belongsTo' => 'presenca');

        $id = new Zend_Form_Element_Hidden("treino_id", $opcoesPadrao);
        $this->addElement($id);

        //Listagem dos atletas
        $atletasTable = new Atleta();
        $atletasList = $atletasTable->getAtivos(array('order' => 'nome'));

        $a = new Zend_Form_Element_MultiCheckbox('atleta_id', $opcoesPadrao);
        $a->setRequired(true)
                ->setLabel('')
                ->setFilters(array('StripTags'))
                ->addErrorMessage("Campo requerido");

        //Carregando os options
        foreach ($atletasList as $at) {
            $a->addMultiOption($at['id'], ucwords(strtolower($at['nome'])) . (!empty($at['apelido']) ? " ({$at['apelido']})" : ""));
        }
        $this->addElement($a);
    }

}

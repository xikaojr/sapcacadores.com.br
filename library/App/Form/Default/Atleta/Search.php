<?php

class App_Form_Default_Pessoa_FisicaBusca extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);


        $opcoesPadrao = array('required' => false);

        $e = new Zend_Form_Element_Hidden('tipo_pessoa', $opcoesPadrao);
        $e->setValue(Pessoa::FISICA);
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('codigo', $opcoesPadrao);
        $e->setLabel($this->_translate->_('Codigo'))
                ->setAttribs(array('class' => 'numeros'))
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('nome', $opcoesPadrao);
        $e->setLabel($this->_translate->_('Nome'))
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('email', $opcoesPadrao);
        $e->setLabel($this->_translate->_('E-mail'));
        $this->addElement($e);

        $cpf = new Zend_Form_Element_Text('cpf', $opcoesPadrao);
        $cpf->setLabel($this->_translate->_('CPF'))
                ->setFilters(array('StripTags', 'Digits'))
                ->setAttribs(array('class' => 'cpf'));
        $this->addElement($cpf);

        $nomeCracha = new Zend_Form_Element_Text('nome_cracha', $opcoesPadrao);
        $nomeCracha->setLabel($this->_translate->_('Como deseja ser chamado'))
                ->setFilters(array('StripTags'));
        $this->addElement($nomeCracha);

        $nascimento = new Zend_Form_Element_Text('data_nascimento', $opcoesPadrao);
        $nascimento->setLabel($this->_translate->_('Data de Nascimento'))
                ->setFilters(array('StripTags'))
                ->setAttribs(array('class' => 'mask-date'));
        $this->addElement($nascimento);

        $sexo = new Zend_Form_Element_Select('sexo', $opcoesPadrao);
        $sexo->addMultiOptions(array('' => $this->_translate->_('Selecione')))
                ->addMultiOptions(array('M' => $this->_translate->_('Masculino')))
                ->addMultiOptions(array('F' => $this->_translate->_('Feminino')))
                ->setLabel($this->_translate->_('Sexo'))
                ->setFilters(array('StripTags'));
        $this->addElement($sexo);

        $e = new Zend_Form_Element_Select('status', $opcoesPadrao);
        $e->setLabel($this->_translate->_('Situacao'))
                ->setFilters(array('StripTags'));
        $e->addMultiOptions(App_Status::statusPessoa());
        $this->addElement($e);
    }

}

<?php

class App_Form_Default_Login extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('frmLogin')
                ->setAttribs(array(
                    'name' => 'frmLogin',
                    'id' => 'frmLogin'
                ))
                ->setMethod('POST');

        $opcoesPadrao = array('belongsTo' => 'login');

        $login = new Zend_Form_Element_Text('login', $opcoesPadrao);
        $login->setRequired(true)
                ->setLabel('Login')
                ->setFilters(array('StripTags'))
                ->addErrorMessage('Campo requerido');
        $this->addElement($login);

        $senha = new Zend_Form_Element_Password('senha', $opcoesPadrao);
        $senha->setRequired(true)
                ->setLabel('Senha')
                ->setAttrib('placeholder', 'Senha:')
                ->setFilters(array('StripTags'))
                ->addErrorMessage('Campo requerido');
        $this->addElement($senha);
    }

}

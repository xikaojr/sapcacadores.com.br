<?php

class App_Form_Default_Pessoa_Contato extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('formPessoaContato')
                ->setAttribs(array(
                    'name' => 'formPessoaContato',
                    'id' => 'formPessoaContato'
                ))->setMethod('POST');

        $opcoesPadrao = array('belongsTo' => 'pessoa_contato');

        $id = new Zend_Form_Element_Hidden('id', $opcoesPadrao);
        $this->addElement($id);

        $pessoaVinculo = new Zend_Form_Element_Hidden('pessoa_vinculo_id', $opcoesPadrao);
        $pessoaVinculo->setRequired(true);
        $this->addElement($pessoaVinculo);

        $tipoContato = new Zend_Form_Element_Select('tipo_contato_id', $opcoesPadrao);
        $tipoContato->setRequired(false)
                ->setLabel($this->_translate->_('Tipo de contato:'))
                ->setFilters(array('StripTags'))
                ->addErrorMessage($this->_translate->_('Campo requerido'));
        $tipoContato->addMultiOptions(
                array(
                    '' => $this->_translate->_('Selecione:'),
                    '1' => $this->_translate->_('Contato principal'),
                    '2' => $this->_translate->_('Recebimento de cobrancas e recibos'),
                    '3' => $this->_translate->_('Responsavel pelo pagamento'),
                    '4' => $this->_translate->_('Representante legal'),
                )
        );
        $this->addElement($tipoContato);


        $nome = new Zend_Form_Element_Text('nome', $opcoesPadrao);
        $nome->setRequired(false)
                ->setLabel($this->_translate->_('Nome:'))
                ->setFilters(array('StripTags'));
        $this->addElement($nome);

        $ddi1 = new Zend_Form_Element_Text('ddi1', $opcoesPadrao);
        $ddi1->setRequired(true)
                ->setLabel($this->_translate->_('DDI:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '2')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi1);

        $fone1 = new Zend_Form_Element_Text('fone1', $opcoesPadrao);
        $fone1->setRequired(true)
                ->setLabel($this->_translate->_('Telefone:'))
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($fone1);

        $ramal1 = new Zend_Form_Element_Text('ramal1', $opcoesPadrao);
        $ramal1->setRequired(false)
                ->setLabel($this->_translate->_('Ramal:'))
                ->setAttrib('maxlength', '5')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ramal1);

        $ddi2 = new Zend_Form_Element_Text('ddi2', $opcoesPadrao);
        $ddi2->setRequired(false)
                ->setLabel($this->_translate->_('DDI2:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '2')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi2);

        $fone2 = new Zend_Form_Element_Text('fone2', $opcoesPadrao);
        $fone2->setRequired(false)
                ->setLabel($this->_translate->_('Telefone 2:'))
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($fone2);

        $ramal2 = new Zend_Form_Element_Text('ramal2', $opcoesPadrao);
        $ramal2->setRequired(false)
                ->setLabel($this->_translate->_('Ramal:'))
                ->setAttrib('maxlength', '5')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ramal2);

        $ddiFax = new Zend_Form_Element_Text('ddi_fax', $opcoesPadrao);
        $ddiFax->setRequired(false)
                ->setLabel($this->_translate->_('DDI Fax:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '2')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddiFax);

        $fax = new Zend_Form_Element_Text('fax', $opcoesPadrao);
        $fax->setRequired(false)
                ->setLabel($this->_translate->_('Fax:'))
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($fax);

        $ramalFax = new Zend_Form_Element_Text('ramal_fax', $opcoesPadrao);
        $ramalFax->setRequired(false)
                ->setLabel($this->_translate->_('Ramal:'))
                ->setAttrib('maxlength', '5')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ramalFax);

        $ddi_celular = new Zend_Form_Element_Text('ddi_celular', $opcoesPadrao);
        $ddi_celular->setRequired(true)
                ->setLabel($this->_translate->_('DDI Cel:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '2')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi_celular);

        $celular = new Zend_Form_Element_Text('celular', $opcoesPadrao);
        $celular->setRequired(true)
                ->setLabel($this->_translate->_('Celular:'))
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($celular);

        $email = new Zend_Form_Element_Text('email', $opcoesPadrao);
        $email->setRequired(false)
                ->setLabel($this->_translate->_('E-mail:'))
                ->setAttribs(array('class' => 'email', 'maxlength' => '60'))
                ->setFilters(array('StripTags'));
        $this->addElement($email);
    }

}

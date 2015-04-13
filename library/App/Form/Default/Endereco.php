<?php

class App_Form_Default_Endereco extends App_Form_Abstract {

    protected $_translate;

    public function __construct($options = null, $tipo = null) {

        parent::__construct($options);

        //Iniciando a library de tradução
        $this->_translate = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('translate');

        $this->setName('formEndereco')
                ->setAttribs(array(
                    'name' => 'formEndereco',
                    'id' => 'formEndereco'
                ))
                ->setMethod('POST');

        $pais = new PaisTable();
        $paises = $pais->getPaises();

        $uf = new Uf();
        $ufs = $uf->getEstados();

        $tipoLogradouro = new TiposLogradouro();
        $tiposLogradouros = $tipoLogradouro->getTiposLogradouro();

        // Verificar qual o tipo de endereco
        $tipoEndereco = ($tipo == null) ? 'endereco' : 'endereco' . $tipo;

        $opcoesPadrao = array('belongsTo' => $tipoEndereco);

        $enderecoId = new Zend_Form_Element_Hidden('endereco_id', $opcoesPadrao);
        $this->addElement($enderecoId);

        $id = new Zend_Form_Element_Hidden('id', $opcoesPadrao);
        $this->addElement($id);

        $pesooa = new Zend_Form_Element_Hidden('pessoa_id', $opcoesPadrao);
        $this->addElement($pesooa);

        $entidade = new Zend_Form_Element_Hidden('entidade_id', $opcoesPadrao);
        $this->addElement($entidade);

        $logradouro = new Zend_Form_Element_Text('logradouro', $opcoesPadrao);
        $logradouro->setRequired(false)
                ->setLabel($this->_translate->_('Endereco:'))
                ->setFilters(array('StripTags'))
                ->addErrorMessage($this->_translate->_('Campo requerido'));
        $this->addElement($logradouro);

        $cep = new Zend_Form_Element_Text('cep', $opcoesPadrao);
        $cep->setRequired(false)
                ->setLabel($this->_translate->_('CEP:'))
                ->setAttrib('class', 'cep')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($cep);

        $numero = new Zend_Form_Element_Text('numero', $opcoesPadrao);
        $numero->setRequired(false)
                ->setLabel($this->_translate->_('Numero:'))
                ->setAttribs(array('maxlength' => '8', 'class' => 'numeros'))
                ->setFilters(array('StripTags'));
        $this->addElement($numero);

        $complemento = new Zend_Form_Element_Text('complemento', $opcoesPadrao);
        $complemento->setRequired(false)
                ->setLabel($this->_translate->_('Complemento:'))
                ->setAttrib('maxlength', '60')
                ->setFilters(array('StripTags'));
        $this->addElement($complemento);

        $bairro = new Zend_Form_Element_Text('bairro', $opcoesPadrao);
        $bairro->setRequired(false)
                ->setLabel($this->_translate->_('Bairro:'))
                ->setAttrib('maxlength', '60')
                ->setFilters(array('StripTags'));
        $this->addElement($bairro);

        $pais = new Zend_Form_Element_Select('pais_id', $opcoesPadrao);
        $pais->setRequired(false)
                ->setLabel($this->_translate->_('Pais:'))
                ->setFilters(array('StripTags'))
                ->setAttrib('class', 'pais');
        $pais->addMultiOptions(array('' => $this->_translate->_('Selecione:')));

        foreach ($paises as $p) {
            $pais->addMultiOptions(array($p['id'] => $p['descricao']));
        }

        $this->addElement($pais);

        $tipoEndereco = new Zend_Form_Element_Select('tipo', $opcoesPadrao);
        $tipoEndereco->setRequired(false)
                ->setLabel($this->_translate->_('Tipo:'))
                ->setFilters(array('StripTags'))
                ->addErrorMessage($this->_translate->_('Campo requerido'))
                ->addMultiOptions(array('' => $this->_translate->_('Selecione:')))
                ->addMultiOptions(array('R' => $this->_translate->_('Residencial')))
                ->addMultiOptions(array('C' => $this->_translate->_('Comercial')));
        $this->addElement($tipoEndereco);

        $tipoLogradouro = new Zend_Form_Element_Select('tipo_logradouro_id', $opcoesPadrao);
        $tipoLogradouro->setRequired(false)
                ->setLabel($this->_translate->_('Tipo de Logradouro:'))
                ->setFilters(array('StripTags'))
                ->setAttrib('class', 'medio ')
                ->addErrorMessage($this->_translate->_('Campo requerido'));
        $tipoLogradouro->addMultiOptions(array('' => $this->_translate->_('Selecione:')));

        foreach ($tiposLogradouros as $t) {
            $tipoLogradouro->addMultiOptions(array($t['id'] => $t['descricao']));
        }

        $this->addElement($tipoLogradouro);

        $uf = new Zend_Form_Element_Select('uf_id', $opcoesPadrao);
        $uf->setRequired(false)
                ->setLabel($this->_translate->_('UF:'))
                ->setAttrib('class', 'endereco_uf pequeno ')
                ->setFilters(array('StripTags'));
        $uf->addMultiOptions(array('' => $this->_translate->_('Selecione:')));

        foreach ($ufs as $u) {
            $uf->addMultiOptions(array($u['id'] => $u['codigo']));
        }

        $this->addElement($uf);

        // obtido por ajax
        $municipio = new Zend_Form_Element_Select('municipio_id', $opcoesPadrao);
        $municipio->setRequired(false)
                ->setLabel($this->_translate->_('Municipio:'))
                ->setAttrib('class', 'endereco-municipio_id medio ')
                ->setFilters(array('StripTags'));
        $municipio->addMultiOptions(array('' => $this->_translate->_('Selecione um estado')));
        $municipio->setRegisterInArrayValidator(false); // Carregado via ajax
        $this->addElement($municipio);

        $cidadeInternacional = new Zend_Form_Element_Text('cidade_internacional', $opcoesPadrao);
        $cidadeInternacional->setRequired(false)
                ->setLabel($this->_translate->_('Cidade:'))
                ->setFilters(array('StripTags'))
                ->addErrorMessage($this->_translate->_('Campo requerido'));
        $this->addElement($cidadeInternacional);

        $ddi1 = new Zend_Form_Element_Text('ddi1', $opcoesPadrao);
        $ddi1->setRequired(false)
                ->setLabel($this->_translate->_('DDI:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '3')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi1);

        $fone1 = new Zend_Form_Element_Text('fone1', $opcoesPadrao);
        $fone1->setRequired(false)
                ->setLabel($this->_translate->_('Telefone:'))
                ->setAttrib('class', 'fone')
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
                ->setAttrib('maxlength', '3')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi2);

        $fone2 = new Zend_Form_Element_Text('fone2', $opcoesPadrao);
        $fone2->setRequired(false)
                ->setLabel($this->_translate->_('Telefone 2:'))
                ->setAttrib('class', 'fone')
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
                ->setAttrib('maxlength', '3')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddiFax);

        $fax = new Zend_Form_Element_Text('fax', $opcoesPadrao);
        $fax->setRequired(false)
                ->setLabel($this->_translate->_('Fax:'))
                ->setAttrib('class', 'fone')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($fax);

        $ramalFax = new Zend_Form_Element_Text('ramal_fax', $opcoesPadrao);
        $ramalFax->setRequired(false)
                ->setLabel($this->_translate->_('Ramal:'))
                ->setAttrib('maxlength', '5')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ramalFax);

        $ddi_celular = new Zend_Form_Element_Text('ddi_celular', $opcoesPadrao);
        $ddi_celular->setRequired(false)
                ->setLabel($this->_translate->_('DDI Cel:'))
                ->setAttrib('class', 'ddi')
                ->setAttrib('maxlength', '3')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($ddi_celular);

        $celular = new Zend_Form_Element_Text('celular', $opcoesPadrao);
        $celular->setRequired(false)
                ->setLabel($this->_translate->_('Celular:'))
                ->setAttrib('class', 'fone')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($celular);

        $correspondencia = new Zend_Form_Element_Radio('corresp', $opcoesPadrao);
        $correspondencia->setRequired(false)
                ->setFilters(array('StripTags'))
                ->setAttrib('class', 'corresp')
                ->setRegisterInArrayValidator(false)
                ->addMultiOption('S', " {$this->_translate->_('Endereco de correspondencia')}");
        $this->addElement($correspondencia);

        $status = new Zend_Form_Element_Checkbox('status', $opcoesPadrao);
        $status->setRequired(false)
                ->setLabel($this->_translate->_('Endereco atualizado?'))
                ->setCheckedValue('2')
                ->setUncheckedValue('1');
        $this->addElement($status);

        $modificadoEm = new Zend_Form_Element_Text('data_atualizacao', $opcoesPadrao);
        $modificadoEm->setRequired(false)
                ->setLabel($this->_translate->_('Atualizado em:'))
                ->setAttribs(array('class' => 'pequeno', 'readonly' => 'readonly'))
                ->setFilters(array('StripTags'));
        $this->addElement($modificadoEm);

        $cargo = new Zend_Form_Element_Text('cargo', $opcoesPadrao);
        $cargo->setRequired(false)
                ->setLabel($this->_translate->_('Cargo'))
                ->setAttribs(array('class' => 'medio input-block-level'))
                ->setFilters(array('StripTags'));
        $this->addElement($cargo);

        $instituicao = new Zend_Form_Element_Text('instituicao', $opcoesPadrao);
        $instituicao->setRequired(false)
                ->setLabel($this->_translate->_('Instituicao'))
                ->setAttribs(array('class' => 'medio input-block-level'))
                ->setFilters(array('StripTags'));
        $this->addElement($instituicao);

        $submit = new Zend_Form_Element_Submit('submitForm');
        $submit->setAttribs(array('id' => 'submitForm'))
                ->setLabel($this->_translate->_('Salvar'));
        $this->addElement($submit);
    }

}

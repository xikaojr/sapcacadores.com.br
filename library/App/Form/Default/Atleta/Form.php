<?php

class App_Form_Default_Atleta_Form extends App_Form_Abstract {

    public function __construct($options = null) {
        parent::__construct($options);

        $local = new Localidade();
        $ufs = $local->getEstados();

        // Permitir a geracao no estilo pessoa[campo]
        $opcoesPadrao = array('belongsTo' => 'atleta');

        // Diretorio para o upload da foto do atleta
        $destinoFoto = PUBLIC_PATH . "arquivos/fotos/";

        if (!is_dir($destinoFoto)) {
            try {
                mkdir($destinoFoto, 0777);
                chmod($destinoFoto, 0777);
            } catch (Exception $e) {
                $destinoFoto = null;
            }
        }
        $id = new Zend_Form_Element_Hidden("id", $opcoesPadrao);
        $this->addElement($id);

        $nome = new Zend_Form_Element_Text('nome', $opcoesPadrao);
        $nome->setRequired(true)
                ->setLabel('Nome')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('maxlength' => '255'))
                ->addErrorMessage('Campo requerido');
        $this->addElement($nome);

        $apelido = new Zend_Form_Element_Text('apelido', $opcoesPadrao);
        $apelido->setLabel('Apelido')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('maxlength' => '100'));
        $this->addElement($apelido);

        $cpf = new Zend_Form_Element_Text('cpf', $opcoesPadrao);
        $cpf->setRequired(false)
                ->setLabel('CPF')
                ->setFilters(array('StripTags', 'Digits'))
                ->setAttribs(array('class' => 'cpf', 'maxlength' => '18'))
                ->addErrorMessage('Campo requerido');
        $this->addElement($cpf);

        $nascimento = new Zend_Form_Element_Text('data_nascimento', $opcoesPadrao);
        $nascimento->setRequired(true)
                ->setLabel('Data de Nascimento')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('class' => 'mask-date'));
        $this->addElement($nascimento);

        $e = new Zend_Form_Element_Text('entrou_em', $opcoesPadrao);
        $e->setRequired(true)
                ->setLabel('Data de Entrada')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('class' => 'datepicker'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('numero_camisa', $opcoesPadrao);
        $e->setRequired(true)
                ->setLabel('Camisa')
                ->setFilters(array('StripTags'))
                ->setAttribs(array('class' => 'numeros', 'maxlength' => '2'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('plano_saude', $opcoesPadrao);
        $e->setRequired(true)
                ->setLabel('Plano de Saúde')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $sexo = new Zend_Form_Element_Select('sexo', $opcoesPadrao);
        $sexo->addMultiOptions(array('' => 'Selecione'))
                ->addMultiOptions(array('M' => 'Masculino'))
                ->addMultiOptions(array('F' => 'Feminino'))
                ->setValue('M')
                ->setRequired(true)
                ->setAttrib('class', 'obrigratorio')
                ->setLabel('Sexo')
                ->setFilters(array('StripTags'));
        $this->addElement($sexo);

        $sexo = new Zend_Form_Element_Select('situacao', $opcoesPadrao);
        $sexo->addMultiOptions(array('' => 'Selecione'))
                ->addMultiOptions(array(Atleta::ATIVO => 'Ativo'))
                ->addMultiOptions(array(Atleta::INATIVO => 'Inativo'))
                ->addMultiOptions(array(Atleta::MACHUCADO => 'Lesionado'))
                ->addMultiOptions(array(Atleta::TECNICO => 'Tecnico'))
                ->setRequired(true)
                ->setLabel('Situação')
                ->setFilters(array('StripTags'));
        $this->addElement($sexo);

        $rg = new Zend_Form_Element_Text('rg', $opcoesPadrao);
        $rg->setRequired(false)
                ->setLabel('RG')
                ->setAttribs(array('maxlength' => '15'))
                ->setFilters(array('StripTags'));
        $this->addElement($rg);

        $passaporte = new Zend_Form_Element_Text('passaporte', $opcoesPadrao);
        $passaporte->setRequired(false)
                ->setLabel('Passaporte')
                ->setAttrib('maxlength', '20')
                ->setFilters(array('StripTags'));
        $this->addElement($passaporte);

        $e = new Zend_Form_Element_Text('altura', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Altura(M)')
                ->setAttrib('class', 'altura')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('peso', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Peso(KG)')
                ->setAttrib('class', 'numeros')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $estadoCivil = new Zend_Form_Element_Select('estado_civil', $opcoesPadrao);
        $estadoCivil->setRequired(false)
                ->addMultiOptions(array('' => 'Selecione'))
                ->addMultiOptions(array(1 => 'Solteiro'))
                ->addMultiOptions(array(2 => 'Casado'))
                ->addMultiOptions(array(3 => 'Viuvo'))
                ->addMultiOptions(array(4 => 'Separado'))
                ->addMultiOptions(array(5 => 'Uniao consensual'))
                ->addMultiOptions(array(6 => 'Divorciado'))
                ->setLabel('Estado civil')
                ->setFilters(array('StripTags'));
        $this->addElement($estadoCivil);

        $e = new Zend_Form_Element_Select("uf", $opcoesPadrao);
        $e->setLabel('Uf:')
                ->setRequired(true)
                ->setFilters(array("StripTags"))
                ->addErrorMessage("Campo requerido");
        foreach ($ufs as $uf) {
            $e->addMultiOptions(array($uf['ufe_sg'] => $uf['ufe_sg']));
        }
        $e->setValue('CE');
        $this->addElement($e);

        $e = new Zend_Form_Element_Select("municipio", $opcoesPadrao);
        $e->setLabel('Naturalidade:')
                ->setRequired(true)
                ->setFilters(array("StripTags"))
                ->addMultiOptions(array('' => 'Selecione uma UF'))
                ->setRegisterInArrayValidator(false)
                ->addErrorMessage("Campo requerido");
        $this->addElement($e);

        $foto = new Zend_Form_Element_File('foto', $opcoesPadrao);
        $foto->setDestination($destinoFoto)
                ->setLabel('Foto')
                ->setRequired(false)
                ->addValidator('Count', false, 1)
                ->addValidator('Size', false, 4194304) // 4MB
                ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
                ->setAttrib('onchange', 'Sistema.readURL(this);')
                ->addErrorMessage('Imagem invalida')
                ->setAllowEmpty(true)
                ->setAutoInsertNotEmptyValidator(false);
        $this->addElement($foto);

//        CAMPOS DE CONTATO --------------------------------------

        $celular = new Zend_Form_Element_Text('celular', $opcoesPadrao);
        $celular->setRequired(false)
                ->setLabel('Celular:')
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($celular);

        $fone1 = new Zend_Form_Element_Text('telefone', $opcoesPadrao);
        $fone1->setRequired(false)
                ->setLabel('Telefone:')
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($fone1);

        $e = new Zend_Form_Element_Text('telefone_contato_emergencia', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Telefone para emergência:')
                ->setAttrib('class', 'fone')
                ->setAttrib('maxlength', '12')
                ->setFilters(array('StripTags', 'Digits'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('nome_contato_emergencia', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Contato emergência:')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('parentesto_contato_emergencia', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Parentesco (emergência):')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $email = new Zend_Form_Element_Text('email', $opcoesPadrao);
        $email->setRequired(false)
                ->setLabel('E-mail:')
                ->setAttribs(array('class' => 'email', 'maxlength' => '60'))
                ->setFilters(array('StripTags'));
        $this->addElement($email);

        //DADOS PARA ENDEREÇO

        $e = new Zend_Form_Element_Text('logradouro', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Endereco:')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('bairro', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Bairro:')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('complemento', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Complemento:')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('log_numero', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Nº:')
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('cep', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Cep:')
                ->setAttribs(array('class' => 'cep', 'maxlength' => '9'))
                ->setFilters(array('StripTags'));
        $this->addElement($e);

        $e = new Zend_Form_Element_Textarea('outras_informacoes', $opcoesPadrao);
        $e->setRequired(false)
                ->setLabel('Outras Informações:')
                ->setAttribs(array('rows' => '3'))
                ->setFilters(array('StripTags'));
        $this->addElement($e);
    }

}

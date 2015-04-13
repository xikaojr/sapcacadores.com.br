<?php

class UtilidadesController extends App_Controller_Default {

    /**
     * Verifica se um determinado cpf consta no banco de dados
     *
     * @param string cpf - Cpf da pessoa
     * @param string id - Id da pessoa
     * @return string JSON
     */
    public function cpfExistAction() {
        $this->noRender();

        $a = new Atleta_Model_Atleta();
        $cpf = trim($this->getRequest()->getParam('cpf', null));
        $id = trim($this->getRequest()->getParam('id', null));

        $retorno = array();
        $retorno['status'] = false;
        $retorno['id'] = '';
        $retorno['nome'] = '';

        if (!empty($cpf)) {
            $atleta = $a->getByCpf($cpf);
            $cpfExiste = (isset($atleta->id) && strlen($atleta->id) > 0);

            if ($cpfExiste && $atleta['id'] == $id) {
                $retorno['status'] = false;
            } else if ($cpfExiste) {
                $retorno['status'] = true;
                $retorno['id'] = $atleta['id'];
                $retorno['nome'] = $atleta['nome'];
            } else {
                $retorno['status'] = false;
            }
        }

        echo $this->_helper->json($retorno);
    }

    /**
     * Verifica se um determinado cnpj consta no banco de dados
     *
     * @param string Cnpj - Cnpj da pessoa
     * @param string id - Id da pessoa
     * @return string JSON
     */
    public function cnpjExistAction() {
        $this->_helper->viewRenderer->setNoRender();

        $p = new Pessoa();
        $cnpj = Zend_Filter_Digits::filter(trim($this->getRequest()->getParam('cnpj', null)));
        $id = trim($this->getRequest()->getParam('id', null));

        $retorno = array();
        $retorno['status'] = false;
        $retorno['nome'] = '';

        $configCliente = Zend_Registry::get('config');
        $configCliente = $configCliente->toArray();

        if (!empty($cnpj)) {

            //Busca da Empresa pelo CNPJ
            $pessoa = $p->findByCnpj($cnpj);

            //Verifica se o retorno é um objeto e se a propriedade id existe
            if (is_object($pessoa) && isset($pessoa->id)) {
                $cnpjExiste = (strlen($pessoa->id) > 0);
            } else {
                $cnpjExiste = false;
            }

// a propria pessoa
            if ($cnpjExiste && $pessoa->id == $id) {
                $retorno['status'] = false;
            }
// pessoa diferente
            else if ($cnpjExiste) {
                $retorno['status'] = true;
                $retorno['id'] = $pessoa->id;
                $retorno['nome'] = $pessoa->razao_social;
            }
// cpf nao existe
            else {
                $retorno['status'] = false;
            }
        }

        echo $this->_helper->json($retorno);
    }

    /**
     * Ação para buscar endereço do CEP no webservice da republica virtual
     *
     * @param POST cep com ou sem formatação
     * @return Json com os dados do endereço
     */
    public function cepAction() {
        $this->noRender()->noLayout();
        $cep = $this->get['cep'];

        if (!empty($cep)) {
            try {
                $localidade = new Localidade();
                $retorno = $localidade->getByCep($cep);
            } catch (Exception $e) {
                $retorno = $e->getMessage();
            }
            
            if ($retorno) {
                $retorno['resultado'] = 1;
            }
        } else {
            $retorno = array('resultado' => '0', 'resultado_txt' => 'CEP desconhecido.');
        }

        echo $this->_helper->json($retorno);
    }

    /**
     * Pegas as cidades de acordo com a UF informada pelo parametro : "uf"
     *
     * @param string uf
     * @return string JSON
     */
    public function getMunicipiosAction() {
        $localidade = new Localidade();

        $retorno = array();
        $uf = trim($this->getRequest()->getParam('uf'));

        if (!empty($uf)) {
            $localidades = $localidade->getByUf($uf);
            $contador = 0;

            $retorno[$contador]['id'] = '';
            $retorno[$contador]['descricao'] = 'Selecione:';
            $contador++;

            foreach ($localidades as $m) {
                $retorno[$contador]['id'] = $m['loc_no'];
                $retorno[$contador]['descricao'] = $m['loc_no'];
                $contador++;
            }
        }

        echo $this->_helper->json($retorno);
    }

}

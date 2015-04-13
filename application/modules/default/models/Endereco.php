<?php

class Endereco extends Devel_Db_Table_Abstract {

    protected $_primary = 'id';
    protected $_name = 'enderecos';
    protected $_alias = 'ende';

    const RESIDENCIAL = 'R';
    const COMERCIAL = 'C';
    const ATUALIZADO = 2;
    const DESATUALIZADO = 1;

    public function retirarCorresp($pessoaId) {
        $endereco = array();
        $endereco['corresp'] = "N";

        return $this->update($endereco, "pessoa_id = '{$pessoaId}'");
    }

    public function findAll($pessoaId, $limit = null, $inicio = null, $order = null) {
        $sql = "SELECT * FROM enderecos WHERE pessoa_id = {$pessoaId}";

        if ($limit)
            $sql .= " limit {$limit}";

        if ($limit && $inicio)
            $sql .= " offset {$inicio}";

        if ($order) {
            $sql .= $order;
        }
//        Zend_Debug::dump($sql);die;
        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    /**
     * Retorna uma colecao de enderecos, baseado-se no id da pessoa e
     * no id da entidade
     *
     * @param int $pessoaId Codigo da pessoa
     * @param int $entidadeId Codigo da entidade
     * @return array
     */
    public function findById($enderecoId) {

        if (empty($enderecoId) || !is_numeric($enderecoId))
            throw new Exception('Voce precisa informar o ID do endereco');

        $select = $this->select()
                ->where($this->getAdapter()->quoteInto('id = ?', $enderecoId));

        $rs = $this->fetchAll($select)->toArray();
        $rs = !empty($rs) ? $rs[0] : array();

        $endereco = new Endereco();
        $endereco->populate($rs);

        return $endereco;
    }

    public function findByPessoaIdAndEntidadeId($pessoaId = null, $cnpj = null, $entidadeId = null, $corresp = null) {

        if (!isset($entidadeId) || $entidadeId === null)
            $entidadeId = $this->_entidadeId;

        $pessoaId = (int) $pessoaId;
        $entidadeId = (int) $entidadeId;

        $sql = "
            select
                psv.id AS vinculo_id,
                ende.*,
                psv.pessoa_fisica_id,
                psv.pessoa_juridica_id,
                psv.endereco_id,
                psv.divisao,
                psv.departamento,
                psv.cargo_id,
                pss.razao_social pessoa_juridica_descricao,
                tl.descricao AS tipo_logradouro,
                p.descricao AS pais,
                m.uf,
                m.descricao AS municipio
            from
                enderecos as ende
                left join pessoas_vinculo psv on psv.endereco_id = ende.id
                left join tipos_logradouros tl on tl.id = ende.tipo_logradouro_id
                left join paises p on p.id = ende.pais_id
                left join municipios m on m.id = ende.municipio_id
                left join pessoas pss on pss.id = ende.pessoa_id
            where
                ende.entidade_id = {$entidadeId}
        ";

        if ($pessoaId != null) {
            $sql .= $this->quoteInto(" AND ende.pessoa_id = ?", $pessoaId);
        } else {
            if ($cnpj != null) {
                $cnpj = $filter->filter($cnpj);
                $sql .= $this->quoteInto(" AND pss.cnpj = ?", $cnpj);
            }
        }

        if ($corresp && !empty($corresp)) {
            $sql .= $this->quoteInto(" and ende.corresp = ?", $corresp);
        }

        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    public function findByCnpjAndEntidadeId($pessoaId = null, $cnpj = null, $entidadeId = null) {
        if (!isset($entidadeId) || $entidadeId === null)
            $entidadeId = $this->_entidadeId;

        $pessoaId = (int) $pessoaId;
        $entidadeId = (int) $entidadeId;

        $sql = "
            select
                ende.*,
                ende.id as endereco_id,
                ende.id as cobr_endereco_id,
                pss.razao_social pessoa_juridica_descricao,
                ende.pessoa_id as pessoa_juridica_id,
                tl.descricao AS tipo_logradouro,
                p.descricao AS pais,
                m.uf,
                m.descricao AS municipio
            from
                enderecos as ende
                left join tipos_logradouros tl on tl.id = ende.tipo_logradouro_id
                left join paises p on p.id = ende.pais_id
                left join municipios m on m.id = ende.municipio_id
                left join pessoas pss on pss.id = ende.pessoa_id
            where
                ende.entidade_id = {$entidadeId}
        ";

        if ($pessoaId != null) {
            $sql .= $this->quote(" AND ende.pessoa_id = ?", $pessoaId);
        } else {
            if ($cnpj != null) {
                $cnpj = $filter->filter($cnpj);
                $sql .= $this->quote(" AND pss.cnpj = ?", $cnpj);
            }
        }
        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    public function findByPessoaIdPrincipal($pessoaId) {

        $pessoaId = (int) $pessoaId;

        $sql = "
            select
                ende.*,
                psv.pessoa_fisica_id,
                psv.pessoa_juridica_id,
                psv.endereco_id,
                psv.divisao,
                psv.departamento,
                psv.cargo_id,
                pss.razao_social pessoa_juridica_descricao
            from
                enderecos as ende
                left join pessoas_vinculo psv on psv.endereco_id = ende.id
                left join pessoas pss on pss.id = psv.pessoa_juridica_id
            where
                ende.pessoa_id = {$pessoaId} AND corresp = 'S'
        ";

        return $this->getDefaultAdapter()->query($sql)->fetch();
    }

    public function getEnderecoPrincipal($pessoaId, $entidadeId) {
        $pessoaId = (int) $pessoaId;
        $entidadeId = (int) $entidadeId;

        $select = $this->select()
                ->where('pessoa_id = ?', $pessoaId)
                ->where('entidade_id = ?', $entidadeId)
                ->where('corresp = ?', 'S')
                ->order('corresp');

        $rs = $this->fetchAll($select)->toArray();

        if (!empty($rs)) {
            return $rs[0];
        } else {
            return array();
        }
    }

    /**
     * Encontra o endereco principal de uma pessoa
     * @param int $pessoaId
     * @param int $entidadeId
     * @return array
     */
    public function findEnderecoCentroCusto($pessoaId, $centroCustoId) {
        $pessoaId = (int) $pessoaId;
        $centroCustoId = (int) $centroCustoId;

        $sql = "
            SELECT ende.*

            FROM enderecos AS ende
            INNER JOIN pessoas_centros_custos AS pcc ON ende.id = pcc.endereco_id

            WHERE pcc.pessoa_id = {$pessoaId}
            AND pcc.centro_custo_id = {$centroCustoId}
            AND pcc.endereco_id IS NOT NULL";

        return end($this->getDefaultAdapter()->query($sql)->fetchAll());
    }

    public function findAllEnderecoCentroCusto($pessoaId, $centroCustoId) {
        $pessoaId = (int) $pessoaId;
        $centroCustoId = (int) $centroCustoId;

        $sql = "
            SELECT ende.*

            FROM enderecos AS ende
            INNER JOIN pessoas_centros_custos AS pcc ON ende.pessoa_id = pcc.pessoa_id

            WHERE pcc.pessoa_id = {$pessoaId}
            AND pcc.centro_custo_id = {$centroCustoId}
            AND pcc.endereco_id IS NOT NULL";

        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    /**
     * Encontra o endereco de correspondencia de uma pessoa
     * @param int $pessoaId
     * @return array (fetchRow)
     * @throws Exception
     */
    public function getEnderecoByPessoaId($pessoaId) {

        $pessoa_id = (int) $pessoaId;

        $sql = "SELECT  
                        e.*, m.descricao municipio, m.uf
                FROM    
                        enderecos e
                        INNER JOIN pessoas p ON p.id = e.pessoa_id
                        LEFT JOIN  municipios m ON e.municipio_id = m.id
                WHERE   
                        e.corresp = 'S'         AND
                        e.pessoa_id = " . $pessoa_id;
        $endereco = $this->getDefaultAdapter()->fetchRow($sql);

        return $endereco;
    }

    public function getAllEnderecoByPessoaId($pessoaId, $tipo = 'F') {

        $sql = "
            select
                ende.*,
                ende.id as endereco_id,
                ende.id as cobr_endereco_id,
                pss.razao_social pessoa_juridica_descricao,
                ende.pessoa_id as pessoa_juridica_id,
                tl.descricao AS tipo_logradouro,
                p.descricao AS pais,
                m.uf,
                m.descricao AS municipio
            from
                enderecos as ende
                inner join pessoas pss on pss.id = ende.pessoa_id and pss.tipo_pessoa = '$tipo'
                left join tipos_logradouros tl on tl.id = ende.tipo_logradouro_id
                left join paises p on p.id = ende.pais_id
                left join municipios m on m.id = ende.municipio_id
            where ";

        $sql .= $this->quoteInto("ende.pessoa_id = ?", $pessoaId);

        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    public function save(array $e) {
        $e = $this->filtro($e);
        return parent::save($e);
    }

    private function filtro(array $dados) {
        $filter = new Zend_Filter_Digits();
        if (isset($dados['cep']))
            $dados['cep'] = $filter->filter($dados['cep']);

        if (isset($dados['fone1']))
            $dados['fone1'] = $filter->filter($dados['fone1']);

        if (isset($dados['fone2']))
            $dados['fone2'] = $filter->filter($dados['fone2']);

        if (isset($dados['fax']))
            $dados['fax'] = $filter->filter($dados['fax']);

        if (isset($dados['celular']))
            $dados['celular'] = $filter->filter($dados['celular']);

        if (!isset($dados['status']) || empty($dados['status']))
            $dados['status'] = 2;

        $dados = App_Filtro::ajusteLimpaCampos($dados, array('cep', 'numero'));

        return $dados;
    }

    public function getUfByParams($params) {

        $sql = " SELECT * FROM ufs WHERE 1=1 "
                . (isset($params["id"]) && !empty($params["id"]) ? " AND id = " . $this->quote($params['id']) : "")
                . (isset($params["codigo"]) && !empty($params["codigo"]) ? " AND codigo = " . $this->quote($params['codigo']) : "");

        return $this->getDefaultAdapter()->fetchRow($sql);
    }

}

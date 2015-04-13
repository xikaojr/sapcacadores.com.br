<?php

/**
 * Sistemas Desenvolvimento
 * 
 * PHP Versao 5.3
 * 
 * @copyright (c) 2014, Itarget tecnologia
 * @link http://www.itarget.com.br
 */

/**
 * Class para controlar as regras de envio dos Modelos de email's
 * @package default
 * @subpackage models
 * @autor Edilson Rodrigues
 */
class ModelosEmails extends Devel_Db_Table_Abstract {

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "modelos_emails";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "id";

    /**
     * Alias da tabela
     * @var string 
     */
    protected $_alias = "mes";

    public function getList($params) {

        $sql = " SELECT {$this->_alias}.*, tes.descricao FROM {$this->_name} as {$this->_alias}
                 INNER JOIN tipos_emails tes on tes.id = {$this->_alias}.tipo_email_id
                 WHERE 1=1 "
                . (isset($params["id"]) && !empty($params["id"]) ? " AND {$this->_alias}.id = " . $this->quote($params["id"]) : "")
                . (isset($params["descricao"]) && !empty($params["descricao"]) ? " AND tes.descricao iLIKE " . $this->quote("%{$params["descricao"]}%") : "")
                . (isset($params["order"]) && !empty($params["order"]) ? " \nORDER BY " . $params["order"] : "\nORDER BY {$this->_alias}.id DESC")
                . (isset($params["limit"]) && !empty($params["limit"]) ? " LIMIT " . $this->quote($params["limit"]) : "")
                . (isset($params["limit"]) && !empty($params["limit"]) && (isset($params["offset"]) && !empty($params["offset"])) ? " OFFSET " . $this->quote($params["offset"]) : "");

        return $this->getAdapter()->query($sql)->fetchAll();
    }

    public function getById($id) {

        $sql = " SELECT {$this->_alias}.*,
                tes.descricao tipo_email_descricao,
                (select descricao from centros_custos ctc where ctc.id = mes.centro_custo_id) as centro_custo_descricao,
                (select descricao from atividades atv where atv.id = mes.atividade_id) as atividade_descricao,
                (select descricao from agenda_atividades aga where aga.id = mes.agenda_atividade_id) as agenda_atividade_descricao,
                (select descricao from categorias_centros_custos ccc where ccc.id = mes.categoria_centro_custo_id) as categoria_centro_custo_descricao
                FROM {$this->_name} as {$this->_alias}
                INNER JOIN tipos_emails tes on tes.id = {$this->_alias}.tipo_email_id
                WHERE 1=1 AND {$this->_alias}.id = {$id}  ";

        return $this->getAdapter()->fetchRow($sql);
    }

    public function getListCount($params) {

        $sql = " SELECT {$this->_alias}.*,tes.descricao FROM {$this->_name} as {$this->_alias}
                 INNER JOIN tipos_emails tes on tes.id = {$this->_alias}.tipo_email_id
                 WHERE 1=1 "
                . (isset($params["id"]) && !empty($params["id"]) ? " AND {$this->_alias}.id = " . $this->quote($params["id"]) : "")
                . (isset($params["descricao"]) && !empty($params["descricao"]) ? " AND tes.descricao iLIKE " . $this->quote("%{$params["descricao"]}%") : "");

        return $this->getAdapter()->fetchOne($sql);
    }

    /**
     * Busca todos os modelos de emails
     * @param array $params condicoes a serem acrescentada ao select
     * @return array
     */
    public function findAll(array $params) {
        $campos = "{$this->_alias}.*"
                . ",tes.descricao";

        if (isset($params['count']) && $params['count'] == true) {
            $campos = "count({$this->_alias}.id) as total";
        }

        $sql = "SELECT 
                    {$campos} 
                  FROM {$this->_name} {$this->_alias}
                  INNER JOIN tipos_emails tes on tes.id = {$this->_alias}.tipo_email_id
                  WHERE
                  {$this->where($params)}    
        ";


        if (!isset($params['count']) && isset($params['ordem']) && count($params['ordem'])) {
            $sql .= " ORDER BY {$params['ordem']['campo']}";
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $sql .= " LIMIT {$params['limit']}";
            if (isset($params['inicio']) && !empty($params['inicio'])) {
                $sql .= " OFFSET {$params['inicio']}";
            }
        }
        return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }

    /**
     * Todas as condicoes usadas no modelos de emails
     * @param array $params parametros para montar condicao
     * @return string
     */
    private function where(array $params) {
        $where = '1 = 1';
        if (isset($params['campo']) && !empty($params['valor'])) {

            if ($params['campo'] == 'id') {
                $params['valor'] = (int) $params['valor'];

                $where = "id = " . $this->quote($params['valor']);
            } else {
                $where = "CAST(unaccent({$params['campo']}) AS text) ILIKE(unaccent(" . $this->quote('%' . $params["valor"] . '%') . "))";
            }
        }
        return $where;
    }

}

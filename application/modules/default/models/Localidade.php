<?php

class Localidade extends Devel_Db_Table_Abstract {

    protected $_name = 'localidade';
    protected $_primary = 'loc_nu';

    public function getEstados() {
        $cacheName = md5('cache-' . $this->_name);

        if (($result = $this->_cache->load($cacheName)) === false) {
            $sql = "select * from {$this->_name} order by loc_no";
            $result = $this->getDefaultAdapter()->query($sql)->fetchAll();
            $this->_cache->save($result, $cacheName);
        }

        return $result;
    }

    /**
     * Retorna os estados para montagem de combobox
     * @return array (fetchPairs)
     */
    public function getPairsEstados() {
        $cacheName = md5('cache-pairs' . $this->_name);

        if (($result = $this->_cache->load($cacheName)) === false) {
            $sql = "SELECT loc_nu, ufe_sg FROM {$this->_name} order by loc_no";
            $result = $this->getDefaultAdapter()->fetchPairs($sql);
            $this->_cache->save($result, $cacheName);
        }

        return $result;
    }

    /**
     * Retorna os estados de acordo com a UF
     * @return array ()
     */
    public function getByUf($Uf) {
        $sql = "select * from {$this->_name} where ufe_sg = '{$Uf}'";
        return $this->getAdapter()->query($sql)->fetchAll();
    }

    public function getByCep($cep) {
        
        $filter = new Zend_Filter_Digits();
        $cep = $filter->filter($cep);
        
        $sql = " SELECT loc.ufe_sg AS estado,
                    loc.loc_no AS cidade,
                    loc.loc_nu AS cidade_id,
                    loc.loc_no_abrev AS cidade_abrev,
                    log.tlo_tx AS tipo,
                    COALESCE(log.log_no, 'CEP de Localidade') AS rua,
                    COALESCE(log.cep, loc.cep) AS cep,
                    COALESCE(log.log_complemento, '') AS complemento,
                    COALESCE(bai.bai_no, 'CENTRO') AS bairro
                   FROM localidade loc
                   LEFT JOIN logradouro log ON log.loc_nu = loc.loc_nu
                   LEFT JOIN faixa_bairro fb ON log.cep >= fb.fcb_cep_ini AND log.cep <= fb.fcb_cep_fim
                   LEFT JOIN bairro bai ON fb.bai_nu = bai.bai_nu
                WHERE log.cep = {$cep}
        ";
        return $this->getAdapter()->query($sql)->fetch();
    }

}

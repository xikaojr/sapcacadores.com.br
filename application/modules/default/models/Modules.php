<?php

class Modules extends Devel_Db_Table_Abstract
{
    protected $_name    = "modules";
    protected $_primary = "id";
    protected $_cols    = array("id", "nome");
    protected $_schema  = "acl";


    public function save($dados) {
        
        if( empty ($dados["nome"]) || !isset($dados["nome"]) )
        {
                throw new Exception('Deve ser especificado um nome para o module.');
        }
        else
        {
            $row =  $this->fetchRow(
                        $this->select()->where('nome = ?', $dados["nome"])
                    );
            
            if(!empty ($row) && isset($row))
            {
                return $row["id"];
            }
            else
            {
                return parent::save($dados);
            }
        }
    }
    
}
<?php

class App_Acl extends Zend_Acl {

    protected static $_instance = null;
    private $_usuario_id = 0;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    /**
     * Por padrão todos os resources serão públicos, serão exigidos apenas quando
     * os mesmos forem definidos na tabela de acl.
     *
     * @param  integer $usuario_id
     * @return void
     */
    protected function _initialize($usuario_id) {
        $this->_usuario_id = $usuario_id ? $usuario_id : 0;

        $this->addRole(new Zend_Acl_Role('default'));
        //dá acesso a tudo para a role default
        $this->allow('default');

        $privegios = $this->getPersonPrivileges();
        
        //Tratamento de exluir os resources que estão repetidos
        $privegios = $this->getPrivilegiosDoSistema($privegios);

        if (count($privegios)) {
            foreach ($privegios as $privegio) {
                                
                if (!$this->has($privegio["module"] . "_" . $privegio["controller"])) {
                    $this->add(new Zend_Acl_Resource($privegio["module"] . "_" . $privegio["controller"]));
                }

                if ($privegio["acessar"] == 'N') {
                    $this->deny('default', $privegio["module"] . "_" . $privegio["controller"], $privegio["action"]);
                }
            }
        }
    }

    private function getPersonPrivileges() {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $sql = "SELECT  am.nome module, ac.nome controller, aa.nome \"action\",
                        CASE
                            WHEN apug.id IS NULL THEN 'N'
                        ELSE 'S'
                END as acessar
                ,s.descricao sistema
                FROM acl.modules am
                INNER JOIN acl.controllers ac ON am.id = ac.module_id
                INNER JOIN acl.actions aa ON ac.id = aa.controller_id
                LEFT JOIN acl.permissoes_actions apa ON aa.id = apa.action_id
                LEFT JOIN acl.permissoes ap ON apa.permissao_id = ap.id
                LEFT JOIN acl.permissoes_sistemas ps ON ps.permissao_id = apa.permissao_id
                LEFT JOIN sistemas s ON s.id = ps.sistema_id
                LEFT JOIN acl.permissoes_usuarios_grupos apug ON (
                    ap.id = apug.permissao_id AND
                    (apug.usuario_id = " . $this->_usuario_id . " OR apug.grupo_id IN (SELECT grupo_id FROM acl.usuarios_grupos aug WHERE aug.usuario_id = " . $this->_usuario_id . "))
                )
                WHERE s.id = ".CODIGO_SISTEMA."
                order by s.id";
              
        $privileges = $db->fetchAll($sql);
        
        return $privileges;
    }

    public static function getInstance($usuario_id) {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_initialize($usuario_id);
        }

        return self::$_instance;
    }

    public function isAllowed($usuario_id, $resource=null, $action=null) {
        if (parent::has($resource)) {
            return parent::isAllowed('default', $resource, $action);
        } else {
            return true;
        }
    }
    
    /**
     * Retira os resources repetidos para o sistema em uso
     * 
     * @param array $privilegios
     * @return array $retorno
     */
    public function getPrivilegiosDoSistema(array $privilegios) {
        
        $resourcesDoSistema = array();
        $retorno = array();
        
        foreach ($privilegios as $key=>$p){
            if($p['sistema']==SISTEMA){
                $resourcesDoSistema[] = $p['module']."_".$p['controller']."_".$p['action'];
                $retorno[] = $p;
                unset($privilegios[$key]);
            }
        }
        
        if(is_array($privilegios) && !empty($privilegios)){
            foreach ($privilegios as $key=>$p){
                $resource = $p['module']."_".$p['controller']."_".$p['action'];
                if(!in_array($resource, $resourcesDoSistema)){
                    $p["acessar"]="N";
                    $retorno[]=$p;
                }
            }
        }
        
        return $retorno;
    }

}
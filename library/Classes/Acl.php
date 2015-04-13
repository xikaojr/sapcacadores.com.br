<?php

class Classes_Acl extends Zend_Acl {

    private static $_instance = null;

    private function _initialize() {
        
    }

    public function __construct(Zend_Auth $auth) {

        // Os controllers
        $classAclControllers = new AclControllers();
        $dadosControllers = $classAclControllers->getControllers('sistemas_id IS NULL OR sistemas_id = ' . CODIGO_SISTEMA);

        if (!empty($dadosControllers))
            foreach ($dadosControllers as $c)
                $this->add(new Zend_Acl_Resource(MODULE . '_' . $c['nome']));

        // As Regras - Codigo do usuario
        $classUsuarios = new Usuarios();
        $dadosUsuarios = $classUsuarios->getUsuarios("status = 'S'");

        $temAdmin = false;
        if (!empty($dadosUsuarios)) {
            foreach ($dadosUsuarios as $u) {
                $this->addRole(new Zend_Acl_Role($u['id']));
                if ($u['id'] == '1') {
                    $temAdmin = true;
                }
            }
        }

        // Nega tudo a todos
        $this->deny();

        // Areas livres a todos logados ou nao
        
        if(file_exists(CONFIGS_PATH.'acl/'.CLIENTE.'/acl.ini')){
            $pathAcl = 'acl/'.CLIENTE.'/acl.ini';
        }else{
            $pathAcl = 'acl/acl.ini';
        }
        
        $aclLivre = new Zend_Config_Ini(CONFIGS_PATH . $pathAcl, 'publico');
        $aclLivre = $aclLivre->toArray();

        if (!empty($aclLivre)) {
            foreach ($aclLivre['acl'] as $key => $a) {
                $actions = explode(',', $a);

                if (!$this->has(MODULE . '_' . $key))
                    $this->add(new Zend_Acl_Resource(MODULE . '_' . $key));

                $this->allow(null, MODULE . '_' . $key, $actions);
            }
        }

        // Se o usuario estiver logado no sistema
        if ($auth->hasIdentity()) {

            $authIdentity = $auth->getIdentity();

            // Areas livres a todos os usuarios logados
            $aclPrivado = new Zend_Config_Ini(CONFIGS_PATH . $pathAcl, 'privado');
            $aclLogado = $aclPrivado->toArray();

            if (!empty($aclLogado)) {
                foreach ($aclLogado['acl'] as $PrivateKey => $b) {

                    $actionsPrivate = explode(',', $b);

                    if (!$this->has(MODULE . '_' . $PrivateKey))
                        $this->add(new Zend_Acl_Resource(MODULE . '_' . $PrivateKey));

                    $this->allow(null, MODULE . '_' . $PrivateKey, $actionsPrivate);
                }
            }
            
            // Dando permissoes ao usuario de acordo com seus grupos
            $classAclGruposPermissoes = new AclGruposPermissoes();
            $dadosPermGrup = $classAclGruposPermissoes->getPermissoesAclByGrupos($authIdentity->id, $authIdentity->centros_custos_id);

            if (!empty($dadosPermGrup)) {
                foreach ($dadosPermGrup as $pg) {

                    if (!$this->has(MODULE . '_' . $pg['ctl_nome']))
                        $this->add(new Zend_Acl_Resource(MODULE . '_' . $pg['ctl_nome']));

                    $this->allow($pg['usu_id'], MODULE . '_' . $pg['ctl_nome'], $pg['act_nome']);

                    // List por padrao
                    if ($pg['act_nome'] == 'index') {
                        $this->allow($pg['usu_id'], MODULE . '_' . $pg['ctl_nome'], 'list');
                    }
                }
            }

            // Dando permissoes especificas ao usuario
            $classAclUsuariosPermissoes = new AclUsuariosPermissoes();
            $dadosPermUsua = $classAclUsuariosPermissoes->getPermissoesAclByUsuario($authIdentity->id, $authIdentity->centros_custos_id);

            if (!empty($dadosPermUsua)) {
                foreach ($dadosPermUsua as $us) {
                    if (!$this->has(MODULE . '_' . $us['ctl_nome']))
                        $this->add(new Zend_Acl_Resource(MODULE . '_' . $us['ctl_nome']));

                    $this->allow($us['usu_id'], MODULE . '_' . $us['ctl_nome'], $us['act_nome']);

                    // List por padrao
                    if ($us['act_nome'] == 'index') {
                        $this->allow($us['usu_id'], MODULE . '_' . $us['ctl_nome'], 'list');
                    }
                }
            }
        }

        // Permissao Total ao Usuario (1)
        if ($temAdmin) {
            $this->allow(1);
        }

        Zend_Registry::set('acl', $this);
    }

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_initialize();
        }

        return self::$_instance;
    }

}
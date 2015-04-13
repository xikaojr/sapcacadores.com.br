<?php

class App_Db_Exception extends Exception {
    /* Redefine a exceção para que a mensagem não seja opcional */

    CONST TYPE_INSERT_OR_UPDATE = 0;
    CONST TYPE_DELETE = 1;

    public function __construct($message, $code = 0, $type = self::TYPE_DELETE) {
        
        $message = $this->translateMessage($message, $code, $type);

        /* Garante que tudo é atribuído corretamente */
        parent::__construct($message, $code);
    }

    /* Representação do objeto personalizada no formato string */

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public static function translateMessage($message, $type = self::TYPE_DELETE) {
        
        $cache = Zend_Registry::get('cache');
        $app_cache = $cache->load('app_cache');
        
        if (!empty($app_cache['contraint'])) {
            $constraint = $app_cache['contraint'];
            foreach ($constraint as $key => $c) {
                if (strpos($message, $key) !== false) {
                    switch ($type) {
                        case self::TYPE_DELETE:
                            $message = $c['msg_del'];
                            break;

                        default:
                            return $c['msg_ins_upd'];
                            break;
                    }
                }
            }
        }
        
        return $message;
    }

}

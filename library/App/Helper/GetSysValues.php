<?php
class Zend_View_Helper_GetSysValues extends Zend_View_Helper_Abstract {

    public function GetSysValues($chave) {

        if (!empty($chave)) {
            $obj = new SysValues();
            $result = $obj->fetchRow("chave = '{$chave}'");

            if ($result) {
                $row = $result->toArray();

                $valores = explode("\n", $row["valores"]);

                foreach ($valores as $v) {
                    list($key, $val) = explode("|", $v);
                    $dados[$key] = $val;
                }
            } else {
                $dados[0] = "::Chave de valores desconchecida::";
            }

            return $dados;
        }
    }

}


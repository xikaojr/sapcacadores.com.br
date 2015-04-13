<?php

class App_Debug {

    public static function show($array) {
        if (APP_ENV != "prodution") {
            echo '<pre>';
            print_r($array);
            var_dump($array);
            echo '</pre>';
        }
    }

    public static function mostraMensagemBanco($exception) {
        //return $exception;
        //TEM QUE MELHORAR O RETORNO        
        if (strpos($exception, 'SQLSTATE') === false) {
            return $exception;
        } else {

            preg_match('/ERROR: (.*)/i', $exception, $mensagemRetorno);
            preg_match('/SQL statement(.*)/', $exception, $sqlRetorno);

            $rs = trim($mensagemRetorno[1]);

            if (APP_ENV != 'prodution') {
                if (isset($sqlRetorno[1])) {
                    $rs .= ' <br /> ' . trim($sqlRetorno[1]);
                }
            }

            return $rs;
        }
    }

}

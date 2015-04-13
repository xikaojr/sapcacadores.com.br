<?php

class App_CartaoCredito_Pagamento {

    public static function cielo($params, $descAtividade, $valorTotalAtividade, $numerosGerados, $infoCartao, $controller) {
        $log = new App_Log_File();
        $log->setDirLogFile(APP_PATH . "../data/log/cartao/{$controller->view->clt}/{$controller->view->centroCusto}")
                ->setLogFile('cielo.log');

        switch (Zend_Registry::get('idiomaApp')) {
            case 'en':
                $idioma = App_CartaoCredito_Cielo::IDIOMA_EN;
                break;

            case 'es':
                $idioma = App_CartaoCredito_Cielo::IDIOMA_ES;
                break;

            case 'pt_BR':
                $idioma = App_CartaoCredito_Cielo::IDIOMA_PT;
                break;

            default :
                $idioma = App_CartaoCredito_Cielo::IDIOMA_PT;
                break;
        }

        try {
            //Tivemos que verificar em que modulo estamos para passar a url de rotorno correta
            if (defined('MODULE') && MODULE == 'icase') {
                $urlRetorno = "{$controller->view->serverUrl()}/associado/associa-se/retorno-cielo?numero={$numerosGerados[0]}";
            } else {
                $urlRetorno = "{$controller->view->serverUrl()}/evento/inscricoes/retorno-cielo?numero={$numerosGerados[0]}";
                //$urlRetorno = "{$controller->view->serverUrl()}/" . strtolower(App_Sistema::getSubModuleName()) . "/inscricoes/retorno-cielo?numero={$numerosGerados[0]}";
            }

            $c = new App_CartaoCredito_Cielo($infoCartao['numero_filiacao']);
            $c->setWebservice(App_CartaoCredito_Cielo::URL_PRODUCAO);

            if ((int) $params['forma_pagamento_tipo'] > 1) {

                if ($params['forma_pagamento_tipo'] > $infoCartao['qtde_parcelas']) {
                    $params['forma_pagamento_tipo'] = $infoCartao['qtde_parcelas'];
                }

                $c->setTipoParcelamento($infoCartao['tipo_parcelamento'])
                        ->setNumeroParcelas($params['forma_pagamento_tipo']);
            }

            if ($params['forma_pagamento_tipo'] == 'D') {
                $params['forma_pagamento_tipo'] = App_CartaoCredito_Cielo::PAGAMENTO_DEBITO;
            }

            switch ($infoCartao['bandeira_id']) {
                case '13':
                    $params['bandeira'] = App_CartaoCredito_Cielo::BANDEIRA_VISA;
                    break;

                case '14':
                    $params['bandeira'] = App_CartaoCredito_Cielo::BANDEIRA_MASTER;
                    break;

                case '15':
                    $params['bandeira'] = App_CartaoCredito_Cielo::BANDEIRA_DINERS;
                    break;

                default:
                    break;
            }

            $c->setAutorizar(App_CartaoCredito_Cielo::AUTORIZAR_AUTENTICADA_NAO_AUTENTICADA)
                    ->setMoeda(App_CartaoCredito_Cielo::MOEDA_REAL)
                    ->setChave($infoCartao['chave_identificacao'])
                    ->setCapturarAutomaticamente(($infoCartao['captura_automatica'] == 'S' ? true : false))
                    ->setBandeira($params['bandeira'])
                    ->setFormaPagamento($params['forma_pagamento_tipo'])
                    ->setIdioma($idioma)
                    ->setValorTransacao($valorTotalAtividade)
                    ->setUrlRetorno($urlRetorno)
                    ->setNossoNumero($numerosGerados[0])
                    ->setDescricaoTransacao($descAtividade)
                    ->setLog($log)
                    ->realizarTransacao();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $c;
    }

    public static function amex($params, $descAtividade, $valorTotalAtividade, $numerosGerados, $infoCartao, $controller) {
//        $log = new App_Log_File();
//        $log->setDirLogFile(APP_PATH . "../data/log/cartao/{$controller->view->clt}/{$controller->view->centroCusto}")
//                ->setLogFile('amex.log');

        switch (Zend_Registry::get('idiomaApp')) {
            case 'en':
                $idioma = App_CartaoCredito_Amex::IDIOMA_EN;
                break;

            case 'es':
                $idioma = App_CartaoCredito_Amex::IDIOMA_ES;
                break;

            case 'pt_BR':
                $idioma = App_CartaoCredito_Amex::IDIOMA_PT;
                break;

            default :
                $idioma = App_CartaoCredito_Amex::IDIOMA_PT;
                break;
        }

        try {
            //Tivemos que verificar em que modulo estamos para passar a url de rotorno correta
            if (defined('MODULE') && MODULE == 'icase') {
                $urlRetorno = "{$controller->view->serverUrl()}/associado/associa-se/retorno-amex?numero={$numerosGerados[0]}";
            } else {
                $urlRetorno = "{$controller->view->serverUrl()}/evento/inscricoes/retorno-amex?numero={$numerosGerados[0]}";
                //$urlRetorno = "{$controller->view->serverUrl()}/" . strtolower(App_Sistema::getSubModuleName()) . "/inscricoes/retorno-amex?numero={$numerosGerados[0]}";
            }

            $c = new App_CartaoCredito_Amex($infoCartao['numero_filiacao']);

            if ((int) $params['forma_pagamento_tipo'] > 1) {

                if ($params['forma_pagamento_tipo'] > $infoCartao['qtde_parcelas']) {
                    $params['forma_pagamento_tipo'] = $infoCartao['qtde_parcelas'];
                }

                $c->setTipoParcelamento($infoCartao['tipo_parcelamento'])
                        ->setNumeroParcelas($params['forma_pagamento_tipo'])
                        ->setFormaPagamento(App_CartaoCredito_Amex::PAGAMENTO_CREDITO);
            } else if ($params['forma_pagamento_tipo'] == 'D') {
                $c->setNumeroParcelas(1)
                        ->setFormaPagamento(App_CartaoCredito_Amex::PAGAMENTO_DEBITO);
            } else {
                $c->setNumeroParcelas(1)
                        ->setFormaPagamento(App_CartaoCredito_Amex::PAGAMENTO_CREDITO);
            }

            $c->setWebservice(App_CartaoCredito_Amex::URL_PRODUCAO)
                    ->setChave($infoCartao['chave_identificacao'])
                    ->setAccessCode($infoCartao['access_code'])
                    ->setIdioma($idioma)
                    ->setValorTransacao($valorTotalAtividade)
                    ->setUrlRetorno($urlRetorno)
                    ->setNossoNumero($numerosGerados[0])
                    ->setDescricaoTransacao($descAtividade)
                    //->setLog($log)
                    ->realizarTransacao();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $c;
    }

}
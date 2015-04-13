<?php

class App_Mail_Template {

    /**
     * Muitas vezes é necessário enviar dados dinâmicos em cada e-mail.
     * Este método substitui palavras chaves no corpo do e-mail, por sua
     * respectiva variável.
     * As palavras chaves são identidicadas por {}
     * A variável $vars deve conter todas as variáveis a serem substituídas
     * em $texto
     *
     * @param string $texto O corpo do e-mail
     * @param array $vars As variáveis
     * @return string
     */
    public static function trocarCamposMagicos($texto, $objetos, $cc = null) {

        /**
         * Campos extras
         * São campos que não estão na tabela, como por exemplo, os links de
         * acesso ao sistema.
         */
        /**
         * Eric Silva
         * Estou comentando este trecho, visto que eo era usado no sistema antigo
          $camposExtras     = array();
          if(isset($vars['PSS_CODIGO'])) {

          $dplCodigo        = $vars['DPL_CODIGO'];
          $modeloCartaDpl   = '';

          // Modelo da carta do palestrante que está sendo enviada
          if( isset($vars['MODELO_CARTA']) && !empty($vars['MODELO_CARTA']) ) {
          $modeloCartaDpl = "&modelo={$vars['MODELO_CARTA']}";
          }

          $host             = "http://{$_SERVER['HTTP_HOST']}";
          $linkCongressista = "<a href='{$host}/congressista/auth/login?cc={$cc}' target='_blank'>{$host}/congressista/auth/login?cc={$cc}&dpl={$dplCodigo}</a>";
          $linkEmpresa      = "<a href='{$host}/empresa/auth/login?cc={$cc}' target='_blank'>{$host}/empresa/auth/login?cc={$cc}&dpl={$dplCodigo}</a>";
          $linkPalestrante  = "<a href='{$host}/palestrante/auth/login?cc={$cc}&dpl={$dplCodigo}{$modeloCartaDpl}' target='_blank'>{$host}/palestrante/auth/login?cc={$cc}&dpl={$dplCodigo}{$modeloCartaDpl}</a>";

          $camposExtras['ESPACO_CONGRESSISTA'] = $linkCongressista;
          $camposExtras['ESPACO_EMPRESA']      = $linkEmpresa;
          $camposExtras['ESPACO_PALESTRANTE']  = $linkPalestrante;
          }
         */
        // Identificando os campos magicos presentes em $texto
        preg_match_all('#(?<!\()\{([^\{]+)\}(?!\))#', $texto, $vars);

        // Campos que recebem o conteudo atraves de metodos especificos
        $camposEspecificos = array(
            'plano_pagamento-valor' => 'procvlrplanopagamento',
            'inscricao-data_vigencia_associacao' => 'procdatavigenciaassociacao'
        );

        foreach ($vars[1] as $var) {

            // Removendo o nome do modelo
            $varPedacos = explode('-', $var);

            try {

                $campo = str_replace($varPedacos[0], '', implode('', $varPedacos));

                if (isset($objetos[$varPedacos[0]])) {
                    // Objeto que contem a variavel
                    $objeto = $objetos[$varPedacos[0]];

                    if (array_key_exists($var, $camposEspecificos)) {
                        $valorCampo = self::$camposEspecificos[$var]($objeto);
                        $texto = str_replace("{{$var}}", $valorCampo, $texto);
                    } else {

                        // Transformar o nome do campo em camelcase, para chamar o metodo get
                        $filtro = new Zend_Filter_Word_UnderscoreToCamelCase();
                        $campoFormatoAntigo = $campo;
                        $campo = $filtro->filter($campo);

                        // Metodo a ser chamado
                        $metodoGet = "get{$campo}";

                        if (method_exists($objeto, $metodoGet)) {

                            $valorCampo = $objeto->$metodoGet();

                            // Eh um campo do tipo data?
                            if (strpos($var, 'data') !== false) {
                                $data = new Zend_Date();
                                $data->setDate($valorCampo, 'yyyy-MM-dd');
                                $valorCampo = $data->toString("dd/MM/yyyy");
                            }

                            $texto = str_replace("{{$var}}", $valorCampo, $texto);
                        } else if (isset($objeto[$campoFormatoAntigo])) {
                            $texto = str_replace("{{$var}}", $objeto[$campoFormatoAntigo], $texto);
                        }
                    }
                }
            } catch (Exception $e) {
                
            }
        }

        return $texto;
    }

    public static function procdatavigenciaassociacao($inscricao) {
        return implode('/', array_reverse(explode('-', $inscricao['ultima_vigencia'])));
    }

    public static function procvlrplanopagamento($plano) {
        $texto = '';

        if ($plano instanceof PlanoPagamento) {

            if ($plano->getQtdeParcela() > 1) {
                $texto .= 'R$ ' . number_format($plano->getValorSocioQd() * $plano->getQtdeParcela(), 2, ',', '.');
                $texto .= " ou {$plano->getQtdeParcela()}X R$ " . number_format($plano->getValorSocioQd(), 2, ',', '.');
            } else {
                $texto .= 'R$ ' . number_format($plano->getValorSocioQd(), 2, ',', '.');
            }
        }

        return $texto;
    }

    /**
     * Procedimento que gera, em uma tabela sem bordas, uma saída contendo
     * as categorias e seus planos de pagamento
     *
     * @param array $vars
     * @return string
     */
    public static function procvlrplanospagamento($vars) {
        $out = '<table border="0" class="PROC_VLR_PLANOS_PAGAMENTO" style="border: none; width: 100%">';

        if (!is_array($vars['PROC_VLR_PLANOS_PAGAMENTO']))
            $vars['PROC_VLR_PLANOS_PAGAMENTO'] = array();

        foreach ($vars['PROC_VLR_PLANOS_PAGAMENTO'] as $p) {
            $out .=
                    '<tr>'
                    . '<td style="border: none;">'
                    . htmlentities($p['CTP_DESCRICAO'])
                    . '</td>'
                    . '<td style="border: none;">R$ '
                    . Plugins_Utilidades::converteValorDouble2String($p['PLP_VALOR'])
                    . '</td>'
                    . '<td style="border: none;">(S&oacute;cio quite: R$ '
                    . Plugins_Utilidades::converteValorDouble2String($p['PLP_VLR_SOCIO'])
                    . ')'
                    . '</td>
      </tr>';
        }
        $out .= '</table>';
        return $out;
    }

    /**
     * Procedimento que gera a listagem do corpo docente de um determinado curso
     *
     * @param array $vars
     * @return string
     */
    /**
      public static function proccorpodocente($vars) {
      $out = '<table border="0" class="PROC_CORPO_DOCENTE" style="border: none; width: 100%">';

      if(!is_array($vars['PROC_CORPO_DOCENTE']))
      $vars['PROC_CORPO_DOCENTE'] = array();

      foreach($vars['PROC_CORPO_DOCENTE'] as $d) {

      $foto = (isset($d['PRF_FOTO'])) ? "<img src='http://icaseweb2.itarget.com.br/{$d['PRF_FOTO']}' border='0' />" : '';
      $out .= "
      <tr>
      <td style='border: none;'>{$foto}</td>
      <td style='border: none;'> "
      . htmlentities($d['PRF_NOME'])
      . "<br />E-mail: {$d['PRF_EMAIL']}
      <br />Titula&ccedil;&atilde;o: "
      . htmlentities($d['PRF_TITULACAO'])
      . "
      </td>
      </tr>";

      }
      $out .= '</table>';
      return $out;
      }
     */
}
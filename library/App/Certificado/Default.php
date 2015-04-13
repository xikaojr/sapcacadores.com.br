<?php

/**
 * Certificado
 *
 * PHP Version 5.3
 *
 * @copyright (c) 2014, Itarget Tecnologia
 * @link http://itarget.com.br
 */

/**
 *
 * @author Edilson Rodrigues
 */
class App_Certificado_Default extends App_Certificado_Abstract {

    /**
     * Gera os certificados do participante
     * @param array $params parametros para gerar do certificado
     * @throws App_Certificado_Exception
     * @throws Zend_Db_Exception
     */
    public function gerar(array $params) {

        $modelosDocumentos = new ModelosDocumentos();
        $pessoa = new Pessoa();
        $pdf = new App_Export_Pdf();
        $participantesEvento = new ParticipantesEvento();
        $controleImpressao = new ControleImpressoes();
        $configuracoes = new Configuracoes();
        $campos = array();

        try {

            if (!isset($params['modelos_documentos_id']) || empty($params['modelos_documentos_id'])) {
                throw new App_Certificado_Exception($this->_translate->_("Modelo de certificado nao informado!"));
            }

            if (!isset($params['pessoa_id']) || empty($params['pessoa_id'])) {
                throw new App_Certificado_Exception($this->_translate->_("Pessoa nao informada!"));
            }

            $rsConfiguracao = $configuracoes->getByCodigo(187);
            if (!$rsConfiguracao) {
                throw new App_Certificado_Exception(sprintf($this->_translate->_("Configuracao de codigo %s nao existe!"), 187));
            }

            $dadosPessoa = $pessoa->findById($params['pessoa_id']);
            $dadosModelosDocumentos = $modelosDocumentos->findById($params['modelos_documentos_id']);

            $rsConfiguracao['valor_referencia'] = explode('|', $rsConfiguracao['valor_referencia']);

            foreach ($rsConfiguracao['valor_referencia'] as $valores) {
                $campo = explode(',', $valores);
                $coluna = trim($campo[1]);
                $indice = $campo[0] . '.' . $coluna;
                if (isset($dadosPessoa[$coluna]) && !empty($dadosPessoa[$coluna])) {
                    $campos[$indice] = $dadosPessoa[$coluna];
                }
                unset($campo, $indice);
            }

            // adicional para imagem de fundo
            $imagem = null; //$imagem = '/images/pdg/icones/certificate.png';

            if (empty($dadosModelosDocumentos['corpo'])) {
                throw new App_Certificado_Exception($this->_translate->_("Nao existe conteudo no modelo definido!"));
            }

            $conteudo = App_Filtro::camposMagicos($campos, $dadosModelosDocumentos['corpo']);

            // margin padrao
            $conteudo = "<div style='padding:20px'>" . $conteudo . "</div>";

            // insere em controle impressoes
            $dados_participantes_evento = $participantesEvento->findParticipanteEventoByCentroCustoId($params['pessoa_id'], $params['centro_custo_id']);

            if (!count($dados_participantes_evento)) {
                throw new App_Certificado_Exception($this->_translate->_('Dados do participante do evento nao encontrado!'));
            }

            $dados = array(
                'credencial_id' => $dados_participantes_evento[0]['credenciais_id'],
                'modelo_documento_id' => $dadosModelosDocumentos['id'],
                'participante_evento_id' => $dados_participantes_evento[0]['id']
            );

            $controleImpressao->save($dados);

            $pdf->exportFromHtmlByCertificado($conteudo, 'certificado', array(App_Utilidades::convertCmForMm($dadosModelosDocumentos['largura']),
                App_Utilidades::convertCmForMm($dadosModelosDocumentos['altura'])), $imagem, 'I');
        } catch (App_Certificado_Exception $e) {
            throw new App_Certificado_Exception($e->getMessage());
        } catch (Zend_Db_Exception $e) {
            throw new Zend_Db_Exception($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retorna os certificados da pessoa, assim como historico de impressoes
     * @param array $params parametros para listar do certificado
     * @return array 
     * @throws App_Certificado_Exception
     */
    public function listar(array $params) {
        $participantesEventoTable = new ParticipantesEvento();
        $controleImpressoesTable = new ControleImpressoes();
        $descricao = $grade = null;
        try {
            if (!isset($params['centro_custo_id']) || empty($params['centro_custo_id'])) {
                throw new App_Certificado_Exception($this->_translate->_("Centro de custo nao informado!"));
            }

            if (!isset($params['pessoa_id']) || empty($params['pessoa_id'])) {
                throw new App_Certificado_Exception($this->_translate->_("Pessoa nao informada!"));
            }

            $retorno = array();
            $dados = $participantesEventoTable->findParticipanteEvento(array(
                'vpa.centro_custo_id' => $params['centro_custo_id'],
                'vpa.pessoa_id' => $params['pessoa_id'],
            ));
            $participante_evento_id = $participantesEventoTable->getParticipanteEventoId(array(
                'pte.centro_custo_id' => $params['centro_custo_id'],
                'pte.pessoa_id' => $params['pessoa_id'])
            );

            if (count($participante_evento_id)) {
                foreach ($dados as $indice => $valor) {
                    $modelo_certificado_id[] = $dados[$indice]['modelo_certificado_id'];
                    $descricao[$dados[$indice]['modelo_certificado_id']] = ModelosDocumentos::getModeloDocumento($valor['tipo_participacao']);
                }
            }

            if (!empty($modelo_certificado_id)) {
                $grade = $controleImpressoesTable->getMaxVia($participante_evento_id, $modelo_certificado_id);
            }

            $retorno['certificados'] = $dados;
            $retorno['historico'] = $grade;
            $retorno['descricao'] = $descricao;
            return $retorno;
        } catch (App_Certificado_Exception $e) {
            throw new App_Certificado_Exception($e->getMessage());
        }
    }

}

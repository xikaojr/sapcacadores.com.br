<?php

class App_Boleto_AtualizarNossoNumero {

    /**
     * Atualiza o campo nosso numero das parcelas das inscricoes informadas.
     * Para isso o controle layout deve ter sido definido no
     * cadastro do agendamento ou na tabela controle_boleto_cartao
     *
     * @param array $inscricoes Lista com as inscricoes a serem atualizadas
     * @param int $controleLayoutId Qual controle layout sera utilizado
     */
    public function atualizarParcelas(array $inscricoes, $controleLayoutId = null, $competencia = null, $contaReceberId = null) {

        $contaReceberTable = new ContaReceber();
        $controleLayoutTable = new ControleLayout();
        $controleBoletoCartaoTable = new ControleBoletoCartao();
        $planoPagamentoTable = new PlanoPagamento();

        // Para cada parcela(ou grupo no mes) sera gerado um nosso numero
        $numerosGerados = $numerosGeradosDoc = array();

        // Para cada parcela(ou grupo no mes) sera gerado um vencimento
        $vencimentoParcela = array();

        // Sera a instancia da classe de nosso numero
        $boletoNossoNumero = null;

        // Primeiro vencimento
        $vencBoleto = new Zend_Date($contaReceberTable->findMinVencimento($inscricoes), 'Y-m-d');

        //iniciando contador da inscricao
        $inicioInscricao = 1;

        //variavel para guardar o id da ultima inscricao gerada
        $inscricaoIdGerado = null;

        foreach ($inscricoes as $inscricaoId) {

            $parcelas = $contaReceberTable->findByInscricaoId($inscricaoId);

            if (count($parcelas)) {
                foreach ($parcelas as $parcela) {

                    if (null !== $controleLayoutId && is_numeric($controleLayoutId)) {
                        $parcela['controle_layout_id'] = $controleLayoutId;
                    }

                    if (empty($parcela['controle_layout_id'])) {
                        continue;
                    }

                    if ($parcela['status'] != ContaReceber::EM_ABERTO) {
                        continue;
                    }

                    if ($contaReceberId && $parcela['id'] != $contaReceberId) {
                        continue;
                    }

                    if (!is_null($inscricaoIdGerado) && $inscricaoIdGerado != $inscricaoId) {
                        $inicioInscricao++;
                    }

                    $controleLayout = $controleLayoutTable->getControleLayout("ctl.id = {$parcela['controle_layout_id']}");
                    $numerosGerados['controleLayoutId'] = $controleLayout['id'];


                    if (empty($boletoNossoNumero)) {
                        $boletoNossoNumero = new Boletos_NossoNumero($controleLayout['codigo_banco']);
                    }

                    //se for a primeira inscricao vai gerar o nosso numero
                    if ($inicioInscricao == 1) {

                        if (!isset($numerosGerados[$parcela['num_parcela']])) {
                            // Atualiza a sequencia
                            $controleLayout['seq_det'] += 1;
                            $controleLayoutTable->save($controleLayout, "id = {$controleLayout['id']}");
                            $numerosGerados[$parcela['num_parcela']] = $boletoNossoNumero->getNossoNumeroSemCarteira($controleLayout['carteira'], $controleLayout['seq_det'], $controleLayout['convenio']);
                            $numerosGeradosDoc[$parcela['num_parcela']] = $controleLayout['seq_det'];
                        }


                        // Gera o controle_boleto_cartao em contas_receber
                        $idControleBoletoCartao = $controleBoletoCartaoTable->save(
                                array(
                                    'tipo_origem' => 1,
                                    'controle_layout_id' => $controleLayout['id'],
                                    'num_boleto' => $numerosGeradosDoc[$parcela['num_parcela']],
                                    'num_nosso_numero' => $numerosGerados[$parcela['num_parcela']],
                                    'data_envio_cobranca' => date('Y-m-d')
                                )
                        );
                    }

                    $sqlUpdateCtr1 = "UPDATE contas_receber SET origem_conta_id = 3, controle_boleto_cartao_id = {$idControleBoletoCartao} WHERE id = {$parcela['id']}";
                    $contaReceberTable->getDefaultAdapter()->query($sqlUpdateCtr1)->execute();

                    // Competencia
                    if ($competencia != null) {
                        $sqlUpdateCtr2 = "UPDATE contas_receber SET competencia = '{$competencia}' WHERE id = {$parcela['id']}";
                        $contaReceberTable->getDefaultAdapter()->query($sqlUpdateCtr2)->execute();
                    }

                    // Vencimento para a parcela(ou grupo do mes)
                    if (!isset($vencimentoParcela[$parcela['num_parcela']])) {

                        /**
                         * @todo ndias
                         */
                        if ($parcela['num_parcela'] == '0' || ($parcela['num_parcela'] == '1' && !isset($vencimentoParcela['0']))) {
                            $infoPlp = $planoPagamentoTable->findById($parcela['plano_pagamento_id']);

                            if (isset($infoPlp['dias_mais_insc']) && $infoPlp['dias_mais_insc'] > 0 && isset($infoPlp['data_fim']) && !empty($infoPlp['data_fim'])) {
                                $vencPlano = new Zend_Date($infoPlp['data_fim'], 'Y-m-d');
                                $vencBoleto = new Zend_Date();

                                $vencBoleto->addDay($infoPlp['dias_mais_insc']);

                                if ($vencBoleto->isLater($vencPlano)) {
                                    $vencBoleto = $vencPlano;
                                }
                            }
                        }

                        $vencimentoParcela[$parcela['num_parcela']] = $vencBoleto->toString('Y-m-d');

                        // O proximo vencimento sera com 30 dias
                        $vencBoleto->addMonth(1);
                    }

                    $dataVencimentoBoleto = $vencimentoParcela[$parcela['num_parcela']];

                    $sqlUpdateCtr = "UPDATE contas_receber SET data_vencimento = '{$dataVencimentoBoleto}', data_referencia_desconto = '{$dataVencimentoBoleto}' WHERE num_parcela = {$parcela['num_parcela']} AND inscricao_id = {$inscricaoId}";
                    $contaReceberTable->getDefaultAdapter()->query($sqlUpdateCtr)->execute();
                }
            }

            $inscricaoIdGerado = $inscricaoId;
        }

        return $numerosGerados;
    }

    public function atualizarContaReceber($contaReceberId, $controleLayoutId = null, $instrucoesBoleto = '') {

        $contaReceberTable = new ContaReceber();
        $controleLayoutTable = new ControleLayout();
        $controleBoletoCartaoTable = new ControleBoletoCartao();

        $parcelas = array($contaReceberTable->findById($contaReceberId));

        if (count($parcelas)) {
            foreach ($parcelas as $parcela) {

                if (null !== $controleLayoutId && is_numeric($controleLayoutId)) {
                    $parcela['controle_layout_id'] = $controleLayoutId;
                }

                if (empty($parcela['controle_layout_id'])) {
                    return false;
                }

                if ($parcela['status'] != ContaReceber::EM_ABERTO) {
                    return false;
                }

                $controleLayout = $controleLayoutTable->getControleLayout("ctl.id = {$parcela['controle_layout_id']}");
                $boletoNossoNumero = new Boletos_NossoNumero($controleLayout['codigo_banco']);

                // Atualiza a sequencia
                $controleLayout['seq_det'] += 1;
                $controleLayoutTable->save($controleLayout, "id = {$controleLayout['id']}");
                $numerosGerados[$parcela['num_parcela']] = $boletoNossoNumero->getNossoNumeroSemCarteira($controleLayout['carteira'], $controleLayout['seq_det'], $controleLayout['convenio']);
                $numerosGeradosDoc[$parcela['num_parcela']] = $controleLayout['seq_det'];

                // Gera o controle_boleto_cartao em contas_receber
                $idControleBoletoCartao = $controleBoletoCartaoTable->save(
                        array(
                            'tipo_origem' => 1,
                            'controle_layout_id' => $controleLayout['id'],
                            'num_boleto' => $numerosGeradosDoc[$parcela['num_parcela']],
                            'num_nosso_numero' => $numerosGerados[$parcela['num_parcela']],
                            'instrucoes_adicionais' => $instrucoesBoleto,
                            'data_envio_cobranca' => date('Y-m-d')
                        )
                );

                $sqlUpdateCtr = "UPDATE contas_receber SET origem_conta_id = 3, controle_boleto_cartao_id = {$idControleBoletoCartao} WHERE id = {$parcela['id']}";
                $contaReceberTable->getDefaultAdapter()->query($sqlUpdateCtr)->execute();
            }
        }
    }

}

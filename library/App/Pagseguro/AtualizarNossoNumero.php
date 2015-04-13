<?php

class App_Pagseguro_AtualizarNossoNumero {

    public function atualizarParcelas(array $params) {
        
        $contaReceberTable = new ContaReceber();
        $controleBoletoCartaoTable = new ControleBoletoCartao();

        $nossoNumero = date('dmYHisu');
        $infoCartao = $params['administradora_cartao_id']; //codigo pagseguro
        foreach ($params['inscricoes'] as $inscricaoId) {

            $parcelas = $contaReceberTable->findByInscricaoId($inscricaoId);

            if (count($parcelas)) {

                foreach ($parcelas as $parcela) {

                    if ($parcela['status'] != ContaReceber::EM_ABERTO) {
                        continue;
                    }

                    if ($params['conta_receber_id'] && $parcela['id'] != $params['conta_receber_id']) {
                        continue;
                    }

                    $idControleBoletoCartao = $controleBoletoCartaoTable->insert(
                            array(
                                'tipo_origem' => 2,
                                'num_nosso_numero' => $nossoNumero,
                                'administradora_cartao_id' => $params['administradora_cartao_id'],
                                'data_envio_cobranca' => date('Y-m-d'),
                                'usuarios_id_inserido' => $params['usuarios_id_inserido']
                            )
                    );

                    $sqlUpdateCtr = "UPDATE contas_receber SET origem_conta_id = 5, controle_boleto_cartao_id = {$idControleBoletoCartao} WHERE id = {$parcela['id']}";
                    $contaReceberTable->getDefaultAdapter()->query($sqlUpdateCtr)->execute();
                }
            }
        }

        return array($nossoNumero);
    }

}

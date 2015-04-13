<?php

class App_CartaoCredito_AtualizarNossoNumero {

    public function atualizarParcelas(array $inscricoes, $bandeira, $centroCusto, $contaReceberId = null) {

        $contaReceberTable = new ContaReceberTable;
        $controleBoletoCartaoTable = new ControleBoletoCartaoTable;
        $controleLayoutCartaoTable = new ControleLayoutCartaoTable;

        $nossoNumero = date('dmYHisu');
        $infoCartao = end($controleLayoutCartaoTable->findBandeirasByCentroCusto($centroCusto, $bandeira));

        foreach ($inscricoes as $inscricaoId) {

            $parcelas = $contaReceberTable->findByInscricaoId($inscricaoId);

            if (count($parcelas)) {

                foreach ($parcelas as $parcela) {

                    if ($parcela['status'] != ContaReceber::EM_ABERTO) {
                        continue;
                    }

                    if ($contaReceberId && $parcela['id'] != $contaReceberId) {
                        continue;
                    }

                    $idControleBoletoCartao = $controleBoletoCartaoTable->insert(
                            array(
                                'tipo_origem' => 2,
                                'administradora_cartao_id' => $infoCartao['bandeira_id'],
                                'num_nosso_numero' => $nossoNumero,
                                'data_envio_cobranca' => date('Y-m-d')
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
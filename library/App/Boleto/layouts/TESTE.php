<?php
$fileReturn = pathinfo(__FILE__,PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . 'retororno.ret';

$data = file($fileReturn);

$header = array(
    'codigo_registro'   => array('de' => '1',   'tamanho' => '1'),
    'codigo_retorno'    => array('de' => '2',   'tamanho' => '1'),
    'codigo_banco'      => array('de' => '77',  'tamanho' => '3'),
    'numero_sequencial' => array('de' => '395', 'tamanho' => '6'),
    'data_geracao'      => array('de' => '95',  'tamanho' => '6'),
    'data_credito'      => array('de' => '380', 'tamanho' => '6'),
    'codigo_cedente'    => array('de' => '27',  'tamanho' => '20')
);

$detalhe = array(
    'codigo_registro'   => array('de' => '1',    'tamanho' => '1'),
    'numero_inscricao'  => array('de' => '4',    'tamanho' => '14'),
    'nosso_numero'      => array('de' => '71',   'tamanho' => '11'),
    'cateira'           => array('de' => '108',  'tamanho' => '1'),
    'ocorrencia'        => array('de' => '109',  'tamanho' => '2'),
    'vencimento'        => array('de' => '147',  'tamanho' => '6'),
    'agencia_cobradora' => array('de' => '169',  'tamanho' => '5'),
    'valor_titulo'      => array('de' => '153',  'tamanho' => '13'),
    'valor_acrescimo'   => array('de' => '189',  'tamanho' => '13'),
    'valor_abatimento'  => array('de' => '228',  'tamanho' => '13'),
    'valor_desconto'    => array('de' => '241',  'tamanho' => '13'),
    'valor_pago'        => array('de' => '254',  'tamanho' => '13'),
    'valor_creditado'   => array('de' => '153',  'tamanho' => '13'),
    'valor_juros'       => array('de' => '267',  'tamanho' => '13'),
    'data_credito'      => array('de' => '296',  'tamanho' => '6'),
    'data_ocorrencia'   => array('de' => '111',  'tamanho' => '6'),
);

$c = 0;
$dataReturn = array();
$totalReturns = count($data);

foreach ($data as $line):

    if ($c == 0):
        foreach ($header as $hk => $hv):
            $dataReturn['header'][$hk] = substr($line,$hv['de'],$hv['tamanho']);
        endforeach;
    elseif ($c >= 0 && $c <= ($totalReturns - 1)):
        foreach ($detalhe as $dk => $dv):
            $dataReturn['detalhe'][][$dk] = substr($line,$dv['de'],$dv['tamanho']);
        endforeach;
    endif;

    $c++;
endforeach;

print_r($dataReturn);

?>

<?php
/**
 * @id M_CED400
 * @author Victor Sobreira (victorcsv@gmail.com)
 * @copyright Itarget Tecnologia
 */
class M_CED400 {
	
	// numero de linhas por leitura
	private $lines_header = '1';
	private $lines_detalhe = '2';
	private $lines_trailer = '1';
	
	// com digito verificador?
	private $digito_verificador = true;
	
	// valores totais
	private $valor_total_tarifa 	= 0.00;
	private $valor_total_titulo 	= 0.00;
	private $valor_total_juros 		= 0.00;
	private $valor_total_multa 		= 0.00;
	private $valor_total_desconto 	= 0.00;
	private $valor_total_abatimento = 0.00;
	private $valor_total_pago 		= 0.00;
	private $valor_total_acrescimo 	= 0.00;
	
	// se tem decinal os valores
	private $decimal = true;
	
	// armazena os erros ocorridos na verficação
	public $error = array();
	
	// dados pra leitura
	private $campos_retorno = array(
		
		// dados do header
		'header' => array(
			// linha 1
			'1' => array(
				'codigo_registro'   => array('de' => '1',   'tamanho' => '1'),
				'codigo_retorno'    => array('de' => '2', 	'tamanho' => '1'),
				'codigo_banco'      => array('de' => '77',  'tamanho' => '3'),
				'numero_sequencial' => array('de' => '395', 'tamanho' => '6'),
				'data_geracao'      => array('de' => '95', 	'tamanho' => '8'),
//				'data_credito'      => array('de' => '208', 'tamanho' => '8'),		//nao tem
//				'agencia'           => array('de' => '54',  'tamanho' => '6'),		//nao tem
//				'conta'             => array('de' => '60',  'tamanho' => '13'),		//nao tem
				'codigo_cedente'    => array('de' => '27',  'tamanho' => '5')
			)
		),
		
		// dados do detalhe
		'detalhe' => array(
            
			// linha 1
			'1' => array(
				'codigo_registro'   => array('de' => '1',   'tamanho' => '1'),		// identificacao do registro
//				'numero_inscricao'  => array('de' => '134', 'tamanho' => '15'),		//nao tem
				'nosso_numero'      => array('de' => '48',  'tamanho' => '15'),
//				'cateira'           => array('de' => '58',  'tamanho' => '1'),		//nao tem
				'vencimento'        => array('de' => '147', 'tamanho' => '6'),
//				'agencia_cobradora' => array('de' => '100', 'tamanho' => '5'),		//nao tem
				'valor_titulo'      => array('de' => '153', 'tamanho' => '13'),     // Valor do titulo
				'valor_acrescimo'   => array('de' => '189', 'tamanho' => '13'), 	//despesas de custas de projeto
				'valor_abatimento'  => array('de' => '228', 'tamanho' => '13'), 	// abatimento concedido
				'valor_desconto'    => array('de' => '241', 'tamanho' => '13'), 	// desconto concedido
				'valor_pago'        => array('de' => '254', 'tamanho' => '13'),		// valor efetivamente pago
//              'valor_creditado'   => array('de' => '153', 'tamanho' => '13'), 	// valor do titulo
				'valor_creditado'   => array('de' => '254', 'tamanho' => '13'), 	// valor do titulo
				'valor_multa'       => array('de' => '280', 'tamanho' => '13'),
				'valor_juros'       => array('de' => '267', 'tamanho' => '13'),
				'data_credito'      => array('de' => '329', 'tamanho' => '8'), 		// data prevista pra lancamento na conta do corrente
				'data_ocorrencia'   => array('de' => '111', 'tamanho' => '6')
			),
			'2' => array(
				'valor_tarifa'      => array('de' => '153', 'tamanho' => '13'),//despesas de cobranca
			)
		)
	);
	/**
	 * Pega o numero de linhas por leitura
	 * 
	 * @return array
	 */
	public function getTotalLines()
    {
		return array(
			'header' => $this->lines_header, 
			'detalhe' => $this->lines_detalhe, 
			'trailer' => $this->lines_trailer
		);
	}
	
	/**
	 * Define se existe digito verificador
	 * 
	 * @return void
	 * @param $dv Boolean[true,false]
	 */
	public function setDV($dv = false)
    {
		$this->digito_verificador = $dv;
	}
	
	/**
	 * Define se o valor tem decimal 
	 * 
	 * @return  void
	 * @param $arg Boolean[true,false]
	 */
	public function setDecimal($arg = false)
    {
		$this->decinal = $arg;
	}

	/**
	 * Pega todos os valores totais
	 * 
	 * @return array
	 */
	public function getTotal()
    {
		return array(
			'tarifa'    => $this->valor_total_tarifa,
			'titulo'    => $this->valor_total_titulo,
			'acrescimo' => $this->valor_total_juros + $this->valor_total_multa,
			'desconto'  => $this->valor_total_desconto + $this->valor_total_abatimento,
			'pago'      => $this->valor_total_pago
		);
	}
	
	/**
	 * Faz a verificação do header do arquivo retorno
	 * 
	 * @return Boolean
	 * @param $line   Integer Número da linha
	 * @param $data   String  Dados da linha
	 * @param $verify Array   Faz uma verificação de valores
	 */
	public function header($line, $data, $verify = array())
    {
		// verifica se existe a linha
		if (isset($this->campos_retorno['header'][$line]) and count($this->campos_retorno['header'][$line]) > 0) {
			// pega os valores dos campos
			foreach ($this->campos_retorno['header'][$line] as $k => $v) {
				$value = substr($data,$v['de']-1,$v['tamanho']);
				$row[$k] = $value;
				
				if ($k == 'codigo_cedente') {
					if (empty($value) or isset($verify[$k]) and $value != $verify[$k]) {
						$this->error[] = 'O convênio do arquivo retorno não é igual ao convenio cadastrado!';
					}
				} elseif ($k == 'codigo_banco') {
					if (empty($value) or $value != '748') {
						$this->error[] = 'O arquivo não é retorno!';
					}
				} elseif ($k == 'codigo_retorno') {
					if (empty($value) or $value != '2') {
						$this->error[] = 'O arquivo não é retorno!';
					}
				}
				unset($value);
			}
		} else {
			$row = array("não existe linha {$line}!");
		}
		
		// retorna o valor pego
		return $row;
	}

	/**
	 * Faz a verificação do detalhe do arquivo retorno
	 * 
	 * @return Array
	 * @param $line Integer
	 * @param $data String
	 */
	public function detalhe($line, $data, $verify = array())
    {
		// verifica se existe a linha
		if (isset($this->campos_retorno['detalhe'][$line]) and count($this->campos_retorno['detalhe'][$line]) > 0) {

            // campos de verificação
            $valores = array(
                'valor_titulo',
                'valor_tarifa',
                'valor_juros',
                'valor_multa',
                'valor_desconto',
                'valor_pago',
                'valor_acrescimo',
                'valor_creditado'
            );

			// pega os valores dos campos
			foreach ($this->campos_retorno['detalhe'][$line] as $k => $v) {
				
				// verifica se tem digito verificador
				$v['tamanho'] = ($this->digito_verificador == true and $k == 'nosso_numero') ? $v['tamanho'] + 1 : $v['tamanho'];

				// pega o valor na linha
				$value = trim(substr($data, ($v['de']-1), $v['tamanho']) );
				
				// verifica os parametros
				if (count($verify) > 0) {
					if (array_key_exists($k,$verify) and !empty($verify[$k]) and $value != $verify[$k]) {
						 $this->error[] = "Erro na verificaçao: Arquivo Retorno - {$k}: {$value} não é válido!";
					}
				}
				
				if ( $this->decimal == true and in_array($k, $valores) ) {
					$value = substr($value, 0, strlen($value) - 2) . '.' .  substr($value, -2,2);
					$value = number_format($value,2);
					
				} elseif ($this->decimal == false and in_array($k,$valores)) {
					$value = number_format($value,2);
					
				}
				
				// verifica os valores 
				$value = (in_array($k,$valores) and empty($value)) ? '0.00' : $value;
				
				// formatando data
				if ($k == 'data_ocorrencia') {
					$value = substr($value,4,4).'-'.substr($value,2,2).'-'.substr($value,0,2);

				} elseif ($k == 'data_credito') {
					$value = substr($value,0,4).'-'.substr($value,4,2).'-'.substr($value,6,2);
				}
				
				// define o valor
				if (in_array($k, array('valor_juros', 'valor_multa', 'valor_acrescimo'))) {
					$row['valor_acrescimo'] += $value;
				} else {
					$row[$k] = $value;
				}
				
				// Faz a soma dos valores totais
				if ($k == 'valor_titulo') {
					$this->valor_total_titulo += (float)$value;
                    
				} elseif ($k == 'valor_tarifa') {
					$this->valor_total_tarifa += (float)$value;

				} elseif ($k == 'valor_juros') {
					$this->valor_total_juros += (float)$value;

				} elseif ($k == 'valor_multa') {
					$this->valor_total_multa += (float)$value;

				} elseif ($k == 'valor_desconto') {
					$this->valor_total_desconto += (float)$value;

				} elseif ($k == 'valor_abatimento') {
					$this->valor_total_abatimento += (float)$value;

				} elseif ($k == 'valor_pago') {
					$this->valor_total_pago += (float)$value;

				} elseif ($k == 'valor_acrescimo') {
					$this->valor_total_acrescimo += (float)$value;

				}
				
				// limpa da memoria
				unset($value);
			}
		} else {
			$row = array("não existe linha {$line}!");
		}
		
		// retorna os valores pego da linha
		return $row;
	}
}
?>
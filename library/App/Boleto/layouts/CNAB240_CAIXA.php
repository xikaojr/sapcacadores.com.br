<?php
/**
 * @id CNAB240
 * @author Victor Sobreira (victorcsv@gmail.com)
 * @copyright Itarget Tecnologia
 */
class CNAB240_CAIXA {
	
	// numero de linhas por leitura
	private $lines_header = '2';
	private $lines_detalhe = '2';
	private $lines_trailer = '2';
	
	// com digito verificador?
	private $digito_verificador = true;
	
	// valores totais
	private $valor_total_tarifa = 0.00;
	private $valor_total_titulo = 0.00;
	private $valor_total_juros = 0.00;
	private $valor_total_multa = 0.00;
	private $valor_total_desconto = 0.00;
	private $valor_total_abatimento = 0.00;
	private $valor_total_pago = 0.00;
	private $valor_total_acrescimo = 0.00;
	
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
				'codigo_registro'   => array('de' => '8',   'tamanho' => '1'),
				'codigo_retorno'    => array('de' => '143', 'tamanho' => '1'),
				'codigo_banco'      => array('de' => '1',   'tamanho' => '3'),
				'numero_sequencial' => array('de' => '158', 'tamanho' => '6'),
				'data_geracao'      => array('de' => '144', 'tamanho' => '8')
			),
			'2' => array(
				'data_credito'      => array('de' => '208', 'tamanho' => '8'),
				'agencia'           => array('de' => '54',  'tamanho' => '6'),
				'conta'             => array('de' => '60',  'tamanho' => '13'),
				'codigo_cedente'    => array('de' => '34',  'tamanho' => '16')
			)
		),
		// dados do detalhe
		'detalhe' => array(
			// linha 1
			'1' => array(
				'codigo_registro'   => array('de' => '8',   'tamanho' => '1'),
				'numero_inscricao'  => array('de' => '134', 'tamanho' => '15'),
				'nosso_numero'      => array('de' => '40',  'tamanho' => '18'),
//				'cateira'           => array('de' => '58',  'tamanho' => '1'),
				'vencimento'        => array('de' => '74',  'tamanho' => '6'),
				'agencia_cobradora' => array('de' => '100', 'tamanho' => '5'),
				'valor_tarifa'      => array('de' => '199', 'tamanho' => '15'),
				'valor_titulo'      => array('de' => '82',  'tamanho' => '15')
			),
			// linha 2
			'2' => array (
				'valor_acrescimo'   => array('de' => '63',  'tamanho' => '15'),
				'valor_desconto'    => array('de' => '33',  'tamanho' => '15'),
//				'valor_abatimento'  => array('de' => '18',  'tamanho' => '15'),//nao tem
				'valor_pago'        => array('de' => '78',  'tamanho' => '15'),
				'valor_creditado'   => array('de' => '93',  'tamanho' => '15'),
				'valor_juros'       => array('de' => '18',  'tamanho' => '15'),
				'data_credito'      => array('de' => '146', 'tamanho' => '8'),
				'data_ocorrencia'   => array('de' => '138', 'tamanho' => '8')
			)
		)
	);
	/**
	 * Pega o numero de linhas por leitura
	 * 
	 * @return array
	 */
	public function getTotalLines() {
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
	public function setDV($dv = false) {
		$this->digito_verificador = $dv;
	}
	
	/**
	 * Define se o valor tem decimal 
	 * 
	 * @return  void
	 * @param $arg Boolean[true,false]
	 */
	public function setDecimal($arg = false) {
		$this->decinal = $arg;
	}

	/**
	 * Pega todos os valores totais
	 * 
	 * @return array
	 */
	public function getTotal() {
		return array(
			'tarifa'    => $this->valor_total_tarifa,
			'titulo'    => $this->valor_total_titulo,
			'acrescimo' => $this->valor_total_acrescimo,
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
	public function header($line, $data, $verify = array()) {
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
	public function detalhe($line, $data, $verify = array()) {
		// verifica se existe a linha
		if (isset($this->campos_retorno['detalhe'][$line]) and count($this->campos_retorno['detalhe'][$line]) > 0) {
			// pega os valores dos campos
			foreach ($this->campos_retorno['detalhe'][$line] as $k => $v) {
				// verifica se tem digito verificador
				$v['tamanho'] = ($this->digito_verificador == true and $k == 'nosso_numero') ? $v['tamanho'] + 1 : $v['tamanho'];
				// pega o valor na linha
				$value = trim(substr($data,$v['de']-1,$v['tamanho']));
				
				// verifica os parametros
				if (count($verify) > 0) {
					if (array_key_exists($k,$verify) and !empty($verify[$k]) and $value != $verify[$k]) {
						 $this->error[] = "Erro na verificaçao: Arquivo Retorno - {$k}: {$value} não é válido!";
					}
				}
				
				// verifica se tem decimal
				$valores = array('valor_titulo','valor_tarifa','valor_juros','valor_multa','valor_desconto','valor_pago','valor_acrescimo','valor_creditado');
				if ($this->decimal == true and in_array($k, $valores)) {
					$value = substr($value,0,strlen($value) - 2) . '.' .  substr($value, -2,2);
					$value = number_format($value,2);
				} elseif ($this->decimal == false and in_array($k,$valores)) {
					$value = number_format($value,2);
				}
				
				// verifica os valores 
				$value = (in_array($k,$valores) and empty($value)) ? '0.00' : $value;
				
				// formatando data
				if ($k == 'data_ocorrencia' or $k == 'data_credito') {
					$value = substr($value,4,4).'-'.substr($value,2,2).'-'.substr($value,0,2);
				}
				
				// define o valor
				if (in_array($k, array('valor_juros', 'valor_multa', 'valor_acrescimo'))) {
					$row['valor_acrescimo'] += $value;
				} else {
					$row[$k] = $value;
				}
				
				// Faz a soma dos valores totais
				if ($k == 'valor_titulo') {
					$this->valor_total_titulo += $value;
				} elseif ($k == 'valor_tarifa') {
					$this->valor_total_tarifa += $value;
				} elseif ($k == 'valor_juros') {
					$this->valor_total_juros += $value;
				} elseif ($k == 'valor_multa') {
					$this->valor_total_multa += $value;
				} elseif ($k == 'valor_desconto') {
					$this->valor_total_desconto += $value;
				} elseif ($k == 'valor_abatimento') {
					$this->valor_total_abatimento += $value;
				} elseif ($k == 'valor_pago') {
					$this->valor_total_pago += $value;
				} elseif ($k == 'valor_acrescimo') {
					$this->valor_total_acrescimo += $value;
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
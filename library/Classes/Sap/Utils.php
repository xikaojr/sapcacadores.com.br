<?php

/**
 * Extende a classe Exception, para o tratamento de exceções
 */
class UtilsException extends Exception {
    
}

/**
 * Classe Utils
 * Diversas funcoes de tratamento e validacao.
 *
 * @package Utils
 * @author Time Avanz (todos@avanzweb.com.br)
 * @copyright Avanz Soluções Criativas
 * @example
 *  try{
 *      print Utils::convertDate(date("d/m/Y"), "sql", "date");
 *      print Utils::transliterate("Soluções Criativas");
 *  } catch(UtilsException $e){
 *      printf("<b>Erro detectado</b>: %s <br /><b>Rota do erro:</b> %s", $e->getMessage(), $e->getTraceAsString() );
 *  } catch(Exception $e){
 *      printf("<b>Erro detectado</b>: %s <br /><b>Rota do erro:</b> %s", $e->getMessage(), $e->getTraceAsString() );
 *  }
 */
final class Sap_Utils {

    /**
     * Lista com caracteres especiais para transliteracao
     * @var array
     */
    private static $specialCharacters = array(
        '/[ÂÀÁÄÃ]/' => 'A',
        '/[âãàáä]/' => 'a',
        '/[ÊÈÉË]/' => 'E',
        '/[êèéë]/' => 'e',
        '/[ÎÍÌÏ]/' => 'I',
        '/[îíìï]/' => 'i',
        '/[ÔÕÒÓÖ]/' => 'O',
        '/[ôõòóö]/' => 'o',
        '/[ÛÙÚÜ]/' => 'U',
        '/[ûúùü]/' => 'u',
        '/ç/' => 'c',
        '/Ç/' => 'C',
        '/[_.\/-]/' => ''
    );

    /**
     * Expressoes de validacao da data
     * @var array
     * @todo Validação para datetime
     */
    private static $isValidDate = array(
        "br" => "^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$",
        "sql" => "^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$"
    );

    /**
     * Expressões para validações diversas
     * @var array
     */
    private static $regexValidations = array(
        "email" => "^[0-9a-zA-Z]+[\_\-\.]?[0-9a-zA-Z]+@(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9]\.)+[A-Za-z]{2,6}|\[[0-9]{1,3}(\.[0-9]{1,3}){3}\])$",
        "url" => "^((ht|f)tp(s?)\:\/\/|~\/|\/){1}([0-9a-zA-Z]+:[0-9a-zA-Z]+@)?([a-zA-Z]{1}([0-9a-zA-Z-]+\.?)*(\.[0-9a-zA-Z]{2,5}){1})$",
        "ip" => "^[0-9]{3}.[0-9]{3}.[0-9]{3}.[0-9]{3}$",
        "cpf" => "^([0-9]){3}.([0-9]){3}.([0-9]){3}-([0-9]){2}$",
        "cep" => "^[0-9]{5}-[0-9]{3}$",
        "phone" => "^\([0-9]{3}\) [0-9]{4}-[0-9]{4}$",
        "date" => "^[0-9]{2}/[0-9]{2}/[0-9]{4}$"
    );

    /**
     * Matriz com as extensões de arquivos permitidas para Download e Upload
     * @var array
     */
    private static $allowedExtensions = array('pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'gif', 'sql');

    /**
     * A classe Utils nao pode ser instanciada nem extendida
     */
    private function __construct() {
        
    }

    public static function UtilsFactory($namespace) {
        $class_name = 'Utils_' . ucfirst($namespace);
        if (class_exists($class_name)) {
            return new $class_name;
        } else {
            throw new UtilsException(sprintf('Namespace %s não encontrado', $namespace));
        }
    }

    /**
     * 
     * @param string $val valor a ser mascarado
     * @param string $mask mascara
     * @return string
     */
    public static function mask($val, $mask) {

        if ($val == "")
            return '';

        if ($mask == 'valor') {
            $val = number_format($val, 2, ",", ".");

            return "R$ " . $val;
        }

        if ($mask == 'data') {
            $val = explode('-', $val);

            return $val[2] . '/' . $val[1] . '/' . $val[0];
        }

        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '9') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    /**
     * Limita a exibição de uma string
     * @param string $string
     * @param int $length
     * @return string
     */
    public static function trimText($string, $length) {
        if (strlen($string) > $length) {
            for ($i = $length; $i <= strlen($string); $i++) {
                if (substr($string, $i, 1) == " ") {
                    return substr($string, 0, $i) . "...";
                }
            }
            return $string;
        } else {
            return $string;
        }
    }

    /**
     * Retira a acentuação e caracteres especiais de uma string
     * @param string $string A string a ser "sanitizada"
     * @param boolean $is_file Determina se a string e um nome de arquivo
     * @return string A string "sanitizada"
     */
    public static function transliterate($string, $is_file = false) {

        $transliterated = preg_replace(
                array_keys(self::$specialCharacters), array_values(self::$specialCharacters), $string
        );

        if ($is_file) {
            $transliterated = str_replace(" ", "-", $transliterated);
            $transliterated = strtolower($transliterated);
        }

        return $transliterated;
    }

    /**
     * Envia ou move um arquivo. Para o identificador, coloque o "name" do campo,
     * caso passe como parâmetro para $file a superglobal $_FILES; senão, utilize "default"
     * 
     * @param array $file O arquivo ($_FILES ou um array)
     * @param string $identifier Um identificador, no caso de $_FILES, o nome do campo.
     * @param string $destination A pasta de destino
     * @param bool $verify Define se a extensão do arquivo deve ser verificada antes do upload
     * @param string $rename O novo nome do arquivo
     * 		default: renomeia o arquivo com  padrao do metodo
     * 		this: o mesmo nome do arquivo
     * 		custom: o nome persnalizado
     * @example
     *  $file = array('default' => array('name' => 'arquivo.ext') );
     *  try{
     *      Utils::sendFile($file, "default", "pasta/destino/", false, 'novo_arquivo');
     *      Utils::sendFile($_FILES, "nome_do_campo", "pasta/destino/", true, 'this');
     *  } catch (UtilsException $e){
     *      printf("Erro detectado: %s", $e->getMessage() );
     *  } catch (Exception $e){
     *      printf("Erro detectado: %s", $e->getMessage() );
     *  }
     * @return array
     *  O nome e o caminho do arquivo enviado
     */
    public static function sendFile(array $file, $identifier, $destination, $verify, $rename = 'default') {

        $file_info = array();
        $filename = (string) "";
        $function = (string) "";
        $source = (string) "";
        $exception = (string) "";
        $new_name = (string) "";

        if (!is_uploaded_file($file[$identifier]['tmp_name'])) {
            try {
                $filename = self::isFile($file[$identifier]['name'], $verify);
            } catch (UtilsException $e) {
                throw new UtilsException($e->getMessage());
            }
        } else
            $filename = $file[$identifier]['name'];

        $extension = self::getExtension($filename);

        switch ($rename) {
            case 'default':
                $new_name = sha1($filename . date('dmYHis'));
                break;
            case 'this':
//$new_name = self::transliterate($filename, true);
                $expFilename = explode(".", $filename);
                $new_name = $expFilename[0];
                break;
            default: $new_name = $rename;
        }
        $new_name = $new_name . "." . $extension;

        if (is_uploaded_file($file[$identifier]['tmp_name'])) {
            $function = "move_uploaded_file";
            $source = $file[$identifier]['tmp_name'];
            $destination = $destination . $new_name;
        } else {
            $function = "copy";
            $source = $filename;
            $destination = $destination . $new_name;
        }

        $function = "move_uploaded_file";

        if (!$function($source, $destination)) {
            if (file_exists($source)) {
                $exception = "O arquivo já existe.";
            } else if (!is_writable($destination)) {
                $exception = "O diretório não tem permissão de escrita.";
            } else {
                $exception = "Erro desconhecido.";
            }

            throw new UtilsException(sprintf("Não foi possível mover o arquivo: %s", $exception));
        }

        @chmod($destination . "/" . $source, 0777);

        $file_info = array(
            'filename' => $new_name,
            'filepath' => $destination,
            'realpath' => realpath($destination),
            'dirname' => pathinfo(realpath($destination), PATHINFO_DIRNAME),
            'extension' => pathinfo(realpath($destination), PATHINFO_EXTENSION),
        );

        return $file_info;
    }

    /**
     * Remove um arquivo de uma pasta específica
     * @param string $file Caminho até chegar o arquivo a ser removido
     * @return boolean TRUE para sucesso durante remoção FALSE para erro.
     */
    public static function removeFile($file) {

        try {

            @chmod($file, 0777);
            unlink($file);

            return true;
        } catch (UtilsException $e) {

            throw new UtilsException($e->getMessage());
            return false;
        } catch (Exception $e) {

            throw new Exception($e->getMessage());
            return false;
        }
    }

    /**
     * Faz o download de $source. Este método deve ser executado antes de qualquer saída HTML
     * @param string $source
     *  O arquivo para download
     * @param bool $verify
     *  Define se a extensão do arquivo deve ser verificada antes do download
     * @return void
     */
    public static function downloadFile($source, $verify) {
        $source = self::isFile($source, $verify);

//Para funcionar no IE
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($source)) . ' GMT');
        header('Cache-Control: private', false);
        header('Content-Type: ' . mime_content_type($source));
        header('Content-Disposition: attachment; filename="' . basename($source) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($source));
        header('Connection: close');
        readfile($source);
        die;
    }

    /**
     * Converte a data $date de $type para $to
     * @param string $date
     *  A data a ser convertida
     * @param string $to
     *  O tipo conversor:
     *      br: para o formato dd/mm/yyyy
     *      sql: para o formato yyyy-mm-dd
     * @param string $type
     *  O tipo (date ou datetime)
     * @return string
     *  A data convertida
     */
    public static function convertDate($date, $to, $type) {
        $return = (string) '';
        switch ($type) {
            case "date":
                switch ($to) {
                    case "sql":
                        $date = self::isValidDate($date, "br");
                        $return = self::dateToSql($date);
                        break;
                    case "br":
                        $date = self::isValidDate($date, "sql");
                        $return = self::dateToBr($date);
                        break;
                    default :
                        throw new UtilsException(sprintf("Parâmetro para formato inválido (%s). Permitidos: %s, %s", $to, 'sql', 'br'));
                }
                break;
            case "datetime":
                switch ($to) {
                    case "sql": $return = self::datetimeToSql($date);
                        break;
                    case "br": $return = self::datetimeToBr($date);
                        break;
                    default :
                        throw new UtilsException(sprintf("Parâmetro para formato inválido (%s). Permitidos: %s, %s", $to, 'sql', 'br'));
                }
                break;
            default :
                throw new UtilsException(sprintf("Parâmetro para tipo inválido (%s). Permitidos: %s, %s", $type, 'date', 'datetime'));
        }

        return $return;
    }

    /**
     * Redimensiona a imagem $source
     * @param string $source
     *  A imagem a ser redimensionada
     * @param string $destination
     *  A pasta de destino
     * @param int $dimension
     *  A dimensão da nova imagem
     * @param string $side
     *  Se a imagem deverá preservar o lado vertical (v) ou horizontal (h)
     * @param string $type
     *  O tipo de redimensionamento
     *      scale: tamanho proporcional
     *      crop:  recortado e quadrado
     *      bigger: proporcional ao maior tamanho
     *      fixed: tamanho de altura fixa
     * @param int $quality
     *  A qualidade da imagem
     * @return void
     */
    public static function imageResize($source, $destination, $dimension, $side = 'v', $type = 'fixed') {

        $extension = self::getExtension($source);
        $function = array(
            'create' => 'imagecreatefrom' . $extension,
            'send' => 'image' . $extension
        );
        $original = $function['create']($source);
        $width = imagesx($original);
        $heigth = imagesy($original);
        $final_width = (int) "";
        $final_height = (int) "";

        if (!file_exists($source)) {
            throw new UtilsException(sprintf("O arquivo %s nao existe", $source));
        }

        if (!$original || !$width || !$heigth) {
            throw new UtilsException(sprintf("Erro ao carregar %s", $source));
        }

        switch ($type) {
            case 'scale':
                switch ($side) {
                    case 'h':
                        $final_width = $dimension;
                        $final_height = $heigth * $dimension / $heigth;
                        break;
                    case 'v':
                        $final_height = $dimension;
                        $final_width = $width * $dimension / $heigth;
                        break;
                    default: throw new UtilsException(sprintf("Parâmetro inválido ou método não suporta o tipo especificado (%s)", $side));
                }
                break;
            case 'crop':
                $width >= $heigth ? $width = $heigth : $heigth = $width;
                $final_width = $dimension;
                $final_height = $dimension;
                break;
            case 'bigger':
                if ($width >= $heigth) {
                    $final_width = $dimension;
                    $final_height = $heigth * $dimension / $width;
                } else {
                    $final_height = $dimension;
                    $final_width = $width * $dimension / $heigth;
                }
                break;
            case 'fixed':
                $final_width = $dimension;
                $final_height = $heigth * $dimension / $width;
                break;
            default: throw new UtilsException(sprintf("Parâmetro inválido ou método não suporta o tipo especificado (%s)", $side));
        }

        $image = imagecreatetruecolor($final_width, $final_height);
        $resampled = imagecopyresampled($image, $original, 0, 0, 0, 0, $final_width + 1, $final_height + 1, $width, $heigth);
        $image_final = $function['send']($image, $destination);

        if (!$resampled || !$image_final) {
            throw new UtilsException("Erro desconhecido");
        }

        imagedestroy($original);
        imagedestroy($image);
    }

    /**
     * Pega a extensão de um arquivo
     * @param string $filename O nome do arquivo
     * @return string A extensão do arquivo
     */
    public static function getExtension($filename) {
        return array_pop(explode(".", $filename));
    }

    
    /**
     * Valida a string $validate, de acordo com o $type especificado
     * @param string $validate
     *  A string a ser validada
     * @param string $type
     *  O tipo avaliador
     * @return string
     *  Em caso de sucesso, retorna a string inalterada
     */
    public static function isValid($validate, $type) {
        if (!array_key_exists($type, self::$regexValidations)) {
            throw new UtilsException('O metodo nao suporta o tipo especificado');
        }

        $check = eregi(self::$regexValidations[$type], $validate);

        if (!$check) {
            throw new UtilsException(sprintf("%s não é um tipo %s válido", $validate, $type));
        }

        return $validate;
    }

    /**
     * Destaca a sintaxe de um arquivo ou string
     * @param string $source Um codigo PHP valido ou um arquivo PHP. Utilize 'this' para exibir
     * o codigo do arquivo atual
     * @return string A sintaxe destacada usando as cores definidas pelo destacador de sintaxe do PHP.
     */
    public static function showSource($source) {
        $as_string = (boolean) true;
        $return = (string) "";
        if (is_file($source)) {
            $return = highlight_file($source, $as_string);
        } else {
            $return = highlight_string($source, $as_string);
        }

        return $return;
    }

    /**
     * Verifica se $file é um arquivo válido
     * @param string $file
     * @param bool $verify
     *  Define se deverá verificar a extensão do arquivo
     * @return string
     *  Em caso de sucesso, retorna $file
     */
    private static function isFile($filename, $verify) {

        if (!is_file($filename)) {
            throw new UtilsException(sprintf("%s não é um arquivo valido", $filename));
        }

        if ($verify) {
            $ext = self::getExtension($filename);
            if (!array_key_exists($ext, self::$allowedExtensions)) {
                throw new UtilsException(sprintf("Extensão de arquivo não permitida (%s)", $ext));
            }
        }

        return $filename;
    }

    /**
     *
     * @param string $module
     *  O nome do módulo, geralmente passado por GET
     * @return boolean
     *  TRUE se o módulo for válido, FALSE em caso de falhas
     */
    public static function moduleIsSecure($module) {
        if (!preg_match(sprintf("#[%s]#", '<>{}$|\'"+=&*%?/!@'), $module)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprime arquivos JS utilizando a classe JSMin
     * @param array $filenames Um array contendo o nome dos arquivos javascript
     * @param string $destination O destino do arquivo comprimido
     * @return string O arquivo comprimido
     */
    public static function fileMinify(array $filenames, $destination) {

        require_once 'jsmin.php';
        if (file_exists($destination)) {
            unlink($destination);
        }
        $minify = array();
        foreach ($filenames as $filename) {
            $minify[] = JSMin::minify(file_get_contents($filename));
        }

        $file = fopen($destination, 'ab');
        if (!$file) {
            throw new UtilsException('O arquivo não pode ser criado.');
        }
        for ($i = 0; $i <= count($minify); $i++) {
            fwrite($file, $minify[$i]);
        }
        fclose($file);

        unset($minify);

        return $file;
    }

    /**
     * Faz a verificação se um valor é vazio ou não.
     * @param string $var Uma string contendo o valor para comparação se é vazio
     * @param string $default Uma string (OPCIONAL) contendo o texto caso o valor passado seja vazio, pro padrão vazio.
     * @return string Retorna ou o padrão, caso seja vazio, ou o valor caso não seja vazio
     * @example
     *  // Exemplo com um valor NãO VAZIO\n
     *  $str   = "Valor";\n
     *  $teste = $Utils::isEmpty($str, "Essa variável está vazia");
     * 
     *  SAÍDA: Valor
     *
     *  // Exemplo com um valor VAZIO
     *  $str   = "";
     *  $teste = $Utils::isEmpty($str, "Essa variável está vazia");
     *
     *  SAÍDA: Essa variável está vazia
     */
    public static function isEmpty($var, $default = "") {

        if ($var && empty($var)) {

            return $default;
        } else {

            return $var;
        }
    }

    /**
     * @param float $price Valor monetário a ser convertido
     * @param string $format SQL para ser inserido na Base de Dados | VIEW para formatação xxx.xxx,xx
     * @return float Retorna o valor convertido
     */
    public static function priceFormat($price, $format = 'SQL') {

// Se for para enviar valor monetário para banco de dados
        if ($format == 'SQL') {

            $priceInit = str_replace(".", "", $price);
            $priceFinal = str_replace(",", ".", $priceInit);
        } else {

// Se o preço a formatar estiver com Vírgula, faz o tratamento
            if (strstr($price, ",")) {

                $priceInit = str_replace(".", "", $price);
                $priceWithDot = str_replace(",", ".", $priceInit);
                $priceFinal = number_format($priceWithDot, 2, ",", ".");

// Caso venha só com ponto, apenas aplica o Number_Format
            } else {

                $priceFinal = number_format($price, 2, ",", ".");
            }
        }

        return $priceFinal;
    }

    /**
     * Faz a verficação se o Domínio de e-mail existe.
     * @param string $email E-mail a ser verificado.
     * @return boolean Retorna TRUE se e-mail for válido e FALSE se e-mail for inválido
     */
    public function VerifyEmailAddress($Email) {
        list($User, $Domain) = explode("@", $Email);
        $Result = checkdnsrr($Domain, 'MX');
        return $Result;
    }

    /**
     * Lista arquivos de um diretório específico
     * @param string $directory Diretório para resgatar arquivos para serem listados
     * @return array Lista de arquivos
     */
    public static function listFilesFromDirectory($directory) {

        try {

            $pointer = opendir($directory);

            while ($nameItens = readdir($pointer)) {
                $files[] = $nameItens;
            }

            $allFiles = array();
            sort($files);

            foreach ($files as $f) {

                if ($f != "." && $f != "..") {

                    $allFiles[] = $f;
                }
            }

            return $allFiles;
        } catch (UtilsException $e) {
            throw new UtilsException($e->getMessage());
        }
    }

    /**
     * Método que faz a leitura completa de um arquivo
     * @param string $filename Caminho completo do arquivo para leitura
     * @return string String com o conteúdo do arquivo indicado em $filename
     */
    public static function readFile($filename) {

        $arquivo = file($filename);
        $strReturn = "";

        foreach ($arquivo as $a) {

            $strReturn .= $a;
        }

        return $strReturn;
    }

    /**
     * Transforma um simples arquivo .ini em um array
     * @param string $filename Caminho completo do arquivo .ini
     * @param boolean $useSessions Seta ou não o uso de sessões. TRUE como padrão
     * @return array Arquivo .ini em forma de array
     */
    public static function parseSimpleIniFile($filename, $useSessions = true) {

        $iniArray = parse_ini_file($filename, $useSessions);

        return $iniArray;
    }

    /**
     * Transforma o nome de um arquivo no padrão cmrupdAAMMDD-01.txt para somente AAMMDD01 para
     * comparações mais exatas
     * @param string $filename Nome do arquivo no padrão cmrupdAAMMDD-01.txt, senda AA o ano, MM o mês e DD o dia
     * @return int Número sequêncial legível para comparações
     */
    public static function dbScriptForNumber($filename) {

        $str = str_replace(".txt", "", $filename);
        $str = str_replace("-", "", $str);
        $str = str_replace("cmrupd", "", $str);
        $str = str_replace("script", "", $str);

        return (int) $str;
    }

    public static function generateJson(array $dataArray) {

        $json = "[{";
        $total = count($dataArray) - 1;
        $counter = 0;

// Gerando um json para uso no javascript
        foreach ($dataArray as $key => $value) {

            if ($counter == $total) {
                $json .= "\"{$key}\":\"{$value}\"";
            } else {
                $json .= "\"{$key}\":\"{$value}\",";
            }

            $counter++;
        }

        $json .= "}]";

        return $json;
    }

    /**
     * Método que faz a abertura, ou criação, de um arquivo e insere um texto dentro do mesmo
     * @param string $filename Caminho completo do arquivo a ser escrito
     * @param string $text Texto que deverá ser inserido no arquivo
     * @return boolean TRUE se arquivo escrito com sucesso e FALSE caso aconteça algum erro durante escrita do arquivo
     */
    public static function openAndWriteFile($filename, $text) {

        try {

            $handle = fopen($filename, "w+");
            fwrite($handle, $text);
            fclose($handle);

            @chmod($filename, 0777);

            return true;
        } catch (UtilsException $e) {

            throw new UtilsException($e->getMessage());
            return false;
        } catch (Exception $e) {

            throw new Exception($e->getMessage());
            return false;
        }
    }

    public static function geraCodigo($length = 8, $str = 'ABCDEFGHIJLMNOPQRSTUVXZ1234567890WYK') {
        return substr(str_shuffle($str), 0, $length);
    }

    public static function converteMetroKM($distancia, $extencao = array()) {
        $metros = $distancia > 999 ? rtrim(number_format(($distancia * 0.001), 3, ",", "."), 0) : $distancia;
        if ($extencao) {
            $retorno = $distancia > 999 ? $metros.' '.$extencao[1] : $metros.' '.$extencao[0];
        }
        return $retorno;
    }

}

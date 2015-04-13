<?php

header('Content-type: text/html; charset=UTF-8');

define('DS', DIRECTORY_SEPARATOR);
define('SERVER_NAME', strtolower($_SERVER['SERVER_NAME']));
//define('SISTEMA', $modulo);
//define('CLIENTE', $cliente);
define('PUBLIC_PATH', dirname(__FILE__) . DS);
define('ROOT_PATH', realpath(PUBLIC_PATH . '..') . DS);
define('APP_PATH', ROOT_PATH . 'application' . DS);
define('MODULES_PATH', APP_PATH . 'modules' . DS);
define('LIBRARY_PATH', ROOT_PATH . 'library' . DS);
define('CONFIGS_PATH', APP_PATH . 'configs' . DS);
//define('DOMINIO', str_replace(SISTEMA . '_', '', SERVER_NAME));
//sistema

ini_set('display_errors', true);
// header("Content-Type: text/html;  charset=UTF-8", true);
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APP_ENV') || define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));

define('DATA_HORA_ATUAL', date('Y-m-d H:i:s'));
define('DATA_HORA_ATUAL_BR', date('d-m-Y H:i:s'));
define('DATA_ATUAL', date('Y-m-d'));
define('DATA_ATUAL_BR', date('d-m-Y'));
define('HORA_ATUAL', date('H:i:s'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APP_ENV, APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();

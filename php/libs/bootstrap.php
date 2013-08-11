<?php
ini_set('xdebug.profiler_enable', 'On');
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', dirname(__DIR__) . '/src');
    
// Define application root path
defined('APPLICATION_ROOT')
    || define('APPLICATION_ROOT', dirname(APPLICATION_PATH));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_ROOT,
    APPLICATION_ROOT . '/libs',
    APPLICATION_PATH,
    get_include_path()
)));

// set global functions
//require_once APPLICATION_ROOT . '/library/functions.php';

define('APPLICATION_ENV', 'development');

require_once 'Zend/Loader/Autoloader.php';
require('Array2XML.php');
Zend_Loader_Autoloader::getInstance()->registerNamespace('Payment')
                                     ->registerNamespace('Communication')
                                     ->registerNamespace('EventManager');


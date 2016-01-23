<?php

header('Content-Type: text/html; charset=utf-8');

define('REAL_PATH', realpath(dirname(__FILE__)));

define('CONFIG_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'config'));

define('FILES_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'files'));

define('APP_ENVIRONMENT', (file_exists(CONFIG_PATH . DIRECTORY_SEPARATOR . '.environment') ? 'development' : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'library'), get_include_path())));

require_once 'Player/Connect/Download.php';

$download = new Player_Connect_Download(
    
    APP_ENVIRONMENT,
    CONFIG_PATH . DIRECTORY_SEPARATOR . 'config.xml'
    
);

date_default_timezone_set('America/Fortaleza');

error_reporting(E_ALL|E_STRICT);

try {
    
    $download->run();
    
} catch (Exception $exception) {
    
    print 'Unable to load modules.';
    
}
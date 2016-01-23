<?php

header('Content-Type: text/html; charset=utf-8');

define('REAL_PATH', realpath(dirname(__FILE__)));

defined('APPLICATION_PATH') ||
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'application'));

defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (file_exists('.environment') ? 'development' : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'library'), get_include_path())));

require_once 'Player.php';

$player = new Player(
    
    APPLICATION_ENV,
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini'
    
);

date_default_timezone_set('America/Fortaleza');

error_reporting(E_ALL|E_STRICT);

try {
    
    $player->run();
    
} catch (Exception $exception) {
    
    print 'Unable to load modules.';
    
}
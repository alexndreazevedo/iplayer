<?php

define('REAL_PATH', realpath(dirname(__FILE__)));

defined('APPLICATION_PATH') ||
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(REAL_PATH . '/library'), get_include_path())));

require_once 'Player/Application.php';

$player = new Player_Application(
    
    APPLICATION_ENV,
    array(
        'file' => APPLICATION_PATH . '/config/config.ini'
    )
    
);

date_default_timezone_set('America/Fortaleza');

error_reporting(E_ALL|E_STRICT);

$player->run();
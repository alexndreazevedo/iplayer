<?php

header('Content-Type: text/html; charset=utf-8');

define('REAL_PATH', realpath(dirname(__FILE__)));

define('CONFIG_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'config'));

define('LAYOUT_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'layout'));

define('FILES_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'files'));

define('APP_ENVIRONMENT', (file_exists(CONFIG_PATH . DIRECTORY_SEPARATOR . '.environment') ? 'development' : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'library'), get_include_path())));

require_once 'Player.php';

$player = new Player(
    
    APP_ENVIRONMENT,
    array(

        'file'      => CONFIG_PATH . DIRECTORY_SEPARATOR . 'config.xml',
        'layout'    => LAYOUT_PATH . DIRECTORY_SEPARATOR,
        
    )
    
);

date_default_timezone_set('America/Fortaleza');

error_reporting(E_ALL|E_STRICT);

try {
    
    $player->run();
    
} catch (Exception $exception) {
    
    print 'Não foi possível carregar os módulos do sistema.';
    
}
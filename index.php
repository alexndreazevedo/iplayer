<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'config.php';

set_include_path(implode(PATH_SEPARATOR, array(realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'library'), get_include_path())));

require_once 'Player.php';

$player = new Player(
    
    APP_ENVIRONMENT,
    array(

        'proxy'     => $proxy,
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
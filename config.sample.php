<?php

define('SERVER', 'player');

define('REAL_PATH', realpath(dirname(__FILE__)));

define('CONFIG_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'config'));

define('LAYOUT_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'layout'));

define('FILES_PATH', realpath(REAL_PATH . DIRECTORY_SEPARATOR . 'files'));

define('APP_ENVIRONMENT', (file_exists(CONFIG_PATH . DIRECTORY_SEPARATOR . '.environment') ? 'development' : 'production'));

$proxy = array(
    'server'    => '193.107.168.26',
    'port'      => 8080,
    'user'      => '',
    'pass'      => '',
);

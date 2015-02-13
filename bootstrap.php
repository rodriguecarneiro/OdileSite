<?php

//DB
define('HOST',  'localhost');
define('DB',    'base');
define('USER',  'root');
define('PASS',  '');

//SYSTEM
define('DS',            DIRECTORY_SEPARATOR);
define('ROOT',          __DIR__.DS);
define('CONF',          ROOT.'config'.DS);
define('CTRL',          ROOT.'controllers'.DS);
define('LIB',           ROOT.'lib'.DS);
define('VIEWS',         ROOT.'public/views'.DS);
define('MODELS',        ROOT.'models'.DS);
define('COMMON',        ROOT.'public/views'.DS.'common'.DS);
define('SITE_IMG',      ROOT.'public'.DS.'assets'.DS.'images'.DS);

//ADMIN
define('ADMIN_VIEWS', ROOT . 'admin' . DS . 'views' . DS);
define('ADMIN_PARTIALS', ROOT . 'admin' . DS . 'views' . DS . 'Partials' . DS);

//PUBLIC
define('BASE_URL', 'http://' . $_SERVER["SERVER_NAME"] . '/');
define('ASSETS', 'http://' . $_SERVER["SERVER_NAME"] . '/public/assets/');
define('CSS', ASSETS . 'css/');
define('JS', ASSETS . 'js/');
define('IMG', ASSETS . 'images/');
<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ERROR);
define(APP_URL, 'http://queue-processing');

define(SECRET, 'EE978E4B133CCBCC29C3F8BF855B2');
define(HOST, '127.0.0.1');
define(DATABASE, 'lpiv_queue_processing');
define(USERNAME, 'admin');
define(PASSWORD, 'Admin@1234');
define(PORT, '3306');
define(DRIVER, 'mysql');
// define(SSL_MODE, 'require');

// define(SECRET, 'EE978E4B133CCBCC29C3F8BF855B2');
// define(HOST, '127.0.0.1');
// define(DATABASE, 'defaultdb');
// define(USERNAME, 'postgres');
// define(PASSWORD, 'root');
// define(PORT, '5432');
// define(DRIVER, 'pgsql');

define(DS, DIRECTORY_SEPARATOR);
define(DIR_APP, __DIR__);
define(DIR_PROJECT, './');

if (file_exists('autoload.php')) {
    include 'autoload.php';
} else {
    // die('Falha ao carregar autoload!');
}
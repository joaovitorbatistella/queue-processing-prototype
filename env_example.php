<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ERROR);

define(SECRET, '');
define(HOST, '');
define(DATABASE, '');
define(USER, '');
define(PASSWORD, '');
define(PORT, '');
define(DRIVER, 'pgsql');
define(SSL_MODE, '');

define(DS, DIRECTORY_SEPARATOR);
define(DIR_APP, __DIR__);
define(DIR_PROJECT, 'webservice_v2');

if (file_exists('autoload.php')) {
    include 'autoload.php';
} else {
    die('Falha ao carregar autoload!');
}
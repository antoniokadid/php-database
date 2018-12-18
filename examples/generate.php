<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Database\Generators\PdoMySqlTableToClassGenerator;
use Database\PdoMySqlConnection;

$username = '';
$password = '';
$dbname = 'test';

$connection = new PdoMySqlConnection('127.0.0.1', 3306, $dbname, $username, $password, 'utf8');

$generator = new PdoMySqlTableToClassGenerator($connection, $dbname);
$generator->generate(__DIR__);
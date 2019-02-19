<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Database\ClassGenerator\ClassGenerator;
use Database\PdoConnection;

$username = '';
$password = '';
$dbname = 'test';

$connection = new PdoConnection('127.0.0.1', 3306, $dbname, $username, $password, 'utf8');

$generator = new ClassGenerator($connection, $dbname);
$generator->namespace = 'Models';

$generator->setNameFormatter(function(int $type, string $name) {
    if ($type == ClassGenerator::FORMAT_CLASS_NAME)
        return preg_replace_callback('/_([a-z])/', function($match) {
            return strtoupper($match[1]);
        }, ucfirst($name));

    if ($type == ClassGenerator::FORMAT_COLUMN_NAME)
        return "`{$name}`";

    if ($type == ClassGenerator::FORMAT_TABLE_NAME)
        return "`{$name}`";

    return preg_replace_callback('/_([a-z])/', function($match) {
        return strtoupper($match[1]);
    }, $name);
});


$generator->addPart(new \Database\ClassGenerator\ClassParts\Properties());
$generator->addPart(new \Database\ClassGenerator\ClassParts\All());
$generator->addPart(new \Database\ClassGenerator\ClassParts\FindByPrimaryKey());
$generator->addPart(new \Database\ClassGenerator\ClassParts\FromArray());
$generator->addPart(new \Database\ClassGenerator\ClassParts\AsArray());
$generator->addPart(new \Database\ClassGenerator\ClassParts\Add());
$generator->addPart(new \Database\ClassGenerator\ClassParts\Update());
$generator->addPart(new \Database\ClassGenerator\ClassParts\Delete());

$generator->generate(__DIR__);
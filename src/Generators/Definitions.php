<?php

namespace Database\Generators;

const FMT_NAME_CLASS = 0;
const FMT_NAME_PROPERTY = 1;

const TAB = '    ';
const EOL = PHP_EOL;

const TYPE_MAP = [
    '/TINYINT/i' => 'int',
    '/SMALLINT/i' => 'int',
    '/MEDIUMINT/i' => 'int',
    '/INT/i' => 'int',
    '/BIGINT/i' => 'int',
    '/FLOAT/i' => 'float',
    '/DOUBLE/i' => 'float',
    '/DECIMAL/i' => 'float',
    '/CHAR/i' => 'string',
    '/VARCHAR/i' => 'string',
    '/TINYTEXT/i' => 'string',
    '/TEXT/i' => 'string',
    '/MEDIUMTEXT/i' => 'string',
    '/LONGTEXT/i' => 'string',
    '/DATE/i' => '\DateTime',
    '/DATETIME/i' => '\DateTime',
    '/TIMESTAMP/i' => '\DateTime',
    '/TIME/i' => '\DateTime',
    '/YEAR/i' => '\DateTime'
];
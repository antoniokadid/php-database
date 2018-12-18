<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class FieldsPart
 *
 * @package Database\Generators\Sections
 */
class FieldsGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $output = TAB . '/** @var IDatabaseConnection */' . EOL;
        $output .= TAB . 'private $connection;';

        return $output;
    }
}
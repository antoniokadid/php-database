<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class PropertiesPart
 *
 * @package Database\Generators\Sections
 */
class PropertiesGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        return rtrim(array_reduce(
            $this->tableColumns,
            function($output, array $prop) {
                $output .= TAB . sprintf('/** @var %s */', $prop['propType']) . EOL;
                $output .= TAB . sprintf('public $%s;', $prop['propName']) . EOL;

                return $output;
            }, ''));
    }
}
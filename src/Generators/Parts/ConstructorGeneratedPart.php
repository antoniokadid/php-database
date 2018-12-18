<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class ConstructorPart
 *
 * @package Database\Generators\Sections
 */
class ConstructorGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $output = TAB . "/**" . EOL;
        $output .= TAB . " * {$this->className} constructor." . EOL;
        $output .= TAB . " * " . EOL;
        $output .= TAB . " * @param IDatabaseConnection|NULL \$connection" . EOL;
        $output .= TAB . " */" . EOL;
        $output .= TAB . "public function __construct(IDatabaseConnection \$connection = NULL) {" . EOL;
        $output .= TAB . "    \$this->connection = \$connection;" . EOL;
        $output .= TAB . "}";

        return $output;
    }
}
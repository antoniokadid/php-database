<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class Properties
 *
 * @package Database\ClassGenerator\ClassParts
 */
class Properties extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        return rtrim(array_reduce(
            $this->tableColumns,
            function ($output, array $prop) {
                $output .= "\t/** @var " . $prop['classPropertyType'] . " */\n";
                $output .= "\tpublic \$" . $prop['classPropertyName'] . ";\n";

                return $output;
            }, ''));
    }
}
<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class AsArray
 *
 * @package Database\ClassGenerator\ClassParts
 */
class AsArray extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $docComment = "";
        $methodName = "asArray";
        $methodBody = "";

        // COMMENT
        $docComment .= "\t/**\n";
        $docComment .= "\t * Convert an instance of {$this->className} into an array.\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return array\n";
        $docComment .= "\t */\n";


        $methodBody .= "\tpublic function {$methodName}(): array {\n";
        $methodBody .= "\t\treturn [\n";

        $methodBody .= $this->getColumnsForInstanceAsArray($this->tableColumns, 3) . "\n\t\t];\n\t}";

        return $docComment . $methodBody;
    }
}
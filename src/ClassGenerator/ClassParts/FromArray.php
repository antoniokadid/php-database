<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class FromArray
 *
 * @package Database\ClassGenerator\ClassParts
 */
class FromArray extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $docComment = "";
        $methodName = "fromArray";
        $methodBody = "";

        // COMMENT
        $docComment .= "\t/**\n";
        $docComment .= "\t * Convert an array into an instance of {$this->className}.\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @param array \$input\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return {$this->className}\n";
        $docComment .= "\t */\n";

        // BODY
        $methodBody .= "\tpublic static function {$methodName}(array \$input): {$this->className} {\n";
        $methodBody .= "\t\t\$instance = new {$this->className}();\n\n";
        $methodBody .= $this->getColumnsForNewClassInstanceFromSerialization($this->tableColumns, 'input', 'instance', 2) . "\n";
        $methodBody .= "\t\treturn \$instance;\n\t}";

        return $docComment . $methodBody;
    }
}
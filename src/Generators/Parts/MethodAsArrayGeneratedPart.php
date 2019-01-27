<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodAsArrayPart
 *
 * @package Database\Generators\Sections
 */
class MethodAsArrayGeneratedPart extends GeneratedPart
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
        $docComment .= TAB . "/**" . EOL;
        $docComment .= TAB . " * Convert an instance of {$this->className} into an array." . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return array" . EOL;
        $docComment .= TAB . " */" . EOL;

        $methodBody .= TAB . "public function {$methodName}(): array {" . EOL;
        $methodBody .= TAB . TAB . "return [";

        foreach ($this->tableColumns as $property)
        {
            $propName = $property['propName'];
            $propType = $property['propType'];
            $colType = $property['colType'];
            $serializedName = $property['propName'];

            $methodBody .= EOL . TAB . TAB . TAB . "'{$serializedName}' => ";

            if ($property['nullable'] === TRUE)
                $methodBody .= "(\$this->{$propName} != NULL) ? ";

            if ($propType === '\DateTime')
            {
                if ($colType === 'DATE')
                    $methodBody .= "\$this->{$propName}->format('Y-m-d')";
                else if ($colType === 'TIME')
                    $methodBody .= "\$this->{$propName}->format('H:i:s')";
                else
                    $methodBody .= "\$this->{$propName}->format('Y-m-d H:i:s')";
            }
            else
                $methodBody .= "\$this->{$propName}";

            if ($property['nullable'] === TRUE)
                $methodBody .= " : NULL,";
            else
                $methodBody .= ",";
        }

        $methodBody .= EOL . TAB . TAB . "];";
        $methodBody .= EOL . TAB . "}";

        return $docComment . $methodBody;
    }
}
<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodFromArrayPart
 *
 * @package Database\Generators\Sections
 */
class MethodFromArrayGeneratedPart extends GeneratedPart
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
        $docComment .= TAB . "/**" . EOL;
        $docComment .= TAB . " * Convert an array into an instance of {$this->className}." . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @param array \$array" . EOL;
        $docComment .= TAB . " * @param IDatabaseConnection|NULL \$connection" . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return {$this->className}" . EOL;
        $docComment .= TAB . " */" . EOL;

        // BODY
        $methodBody .= TAB . "public static function {$methodName}(array \$array, IDatabaseConnection \$connection = NULL): {$this->className} {" . EOL;
        $methodBody .= TAB . TAB . "\$result = new {$this->className}(\$connection);" . EOL;

        foreach ($this->tableColumns as $property)
        {
            $propName = $property['propName'];
            $propType = $property['propType'];
            $serializedName = $property['propName'];

            if ($propType === '\DateTime')
            {
                $methodBody .= EOL . TAB . TAB . "\$value = \\DateTime::createFromFormat('Y-m-d H:i:s', \$array['{$serializedName}'], new \\DateTimeZone('UTC'));";
                $methodBody .= EOL . TAB . TAB . "\$result->{$propName} = (\$value === FALSE) ? NULL : \$value;";
            }
            else
            {
                $methodBody .= EOL . TAB . TAB;

                $methodBody .= "\$result->{$propName} = ";

                if ($property['nullable'] === TRUE)
                    $methodBody .= "(\$array['" . $serializedName . "'] != NULL) ? ";

                if ($propType == 'bool')
                    $methodBody .= "boolval(\$array['" . $serializedName . "'])";
                else if ($propType == 'int')
                    $methodBody .= "intval(\$array['" . $serializedName . "'])";
                else if ($propType == 'float')
                    $methodBody .= "floatval(\$array['" . $serializedName . "'])";
                else if ($propType == 'string')
                    $methodBody .= "strval(\$array['" . $serializedName . "'])";
                else
                    $methodBody .= "\$array['" . $serializedName . "']";

                $methodBody .= ($property['nullable'] === TRUE) ? " : NULL;" : ";";
            }
        }

        $methodBody .= EOL . EOL . TAB . TAB . "return \$result;";
        $methodBody .= EOL . TAB . "}";

        return $docComment . $methodBody;
    }
}
<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodAddPart
 *
 * @package Database\Generators\Sections
 */
class MethodAddGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $docComment = "";
        $methodName = "add";
        $methodBody = "";

        // COMMENT
        $docComment .= TAB . "/**" . EOL;
        $docComment .= TAB . " * Add" . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return bool" . EOL;
        $docComment .= TAB . " * @throws" . EOL;
        $docComment .= TAB . " */" . EOL;

        // BODY PREP
        $dbFields = implode(", ", array_map(function (array $column) { return $column['colName']; }, $this->tableColumns));
        $dbParams = implode(", ", array_fill(0, count($this->tableColumns), "?"));

        // BODY
        $methodBody .= TAB . "public function {$methodName}(): bool {" . EOL;
        $methodBody .= TAB . TAB . "\$sql = \"INSERT INTO {$this->tableName} ({$dbFields}) " . EOL .
                       TAB . TAB . "        VALUES ({$dbParams})\";" . EOL . EOL;
        $methodBody .= TAB . TAB . "return \$this->connection->execute(\$sql, [";

        foreach($this->tableColumns as $column)
        {
            $propName = $column['propName'];
            $propType = $column['propType'];
            $nullable = $column['nullable'];

            $methodBody .= EOL . TAB . TAB . TAB;

            if ($nullable === TRUE)
                $methodBody .= "(\$this->{$propName} != NULL) ? ";

            if ($propType === '\DateTime')
                $methodBody .= "\$this->{$propName}->format('Y-m-d H:i:s')";
            else
                $methodBody .= "\$this->{$propName}";

            if ($column['nullable'] === TRUE)
                $methodBody .= " : NULL,";
            else
                $methodBody .= ",";
        }

        $methodBody .= EOL . TAB . TAB . "]);" . EOL;
        $methodBody .= TAB . "}";

        return $docComment . $methodBody;
    }
}
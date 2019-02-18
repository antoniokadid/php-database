<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class Add
 *
 * @package Database\ClassGenerator\ClassParts
 */
class Add extends ClassPart
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
        $docComment .= "\t/**\n";
        $docComment .= "\t * Add\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @param \\Database\\IDatabaseConnection \$connection\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return bool\n";
        $docComment .= "\t * @throws\n";
        $docComment .= "\t */\n";

        // BODY
        $methodBody .= "\tpublic function {$methodName}(\\Database\\IDatabaseConnection \$connection): bool {\n";

        // BUILD QUERY
        $methodBody .= "\t\t\$sql = \"INSERT INTO {$this->tableName} (" . $this->sqlColumns() . ") VALUES (" . $this->sqlAddColumns() . ")\";";
        $methodBody .= "\n\n";

        $queryParams = $this->getColumnsAsMethodParams($this->tableColumns, TRUE);

        $methodBody .= "\t\treturn \$connection->execute(\$sql, [{$queryParams}]);";

        // END BODY
        $methodBody .= "\n\t}";

        return $docComment . $methodBody;
    }
}
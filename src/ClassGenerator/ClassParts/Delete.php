<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class Delete
 *
 * @package Database\ClassGenerator\ClassParts
 */
class Delete extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $docComment = "";
        $methodName = "delete";
        $methodBody = "";

        $primaryKeys = array_filter(
            $this->tableColumns,
            function (array $column) {
                return $column['isPrimary'] === TRUE;
            });

        // COMMENT
        $docComment .= "\t/**\n";
        $docComment .= "\t * Delete\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @param \\Database\\IDatabaseConnection \$connection\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return bool\n";
        $docComment .= "\t * @throws\n";
        $docComment .= "\t */\n";

        // BODY
        $methodBody .= "\tpublic function {$methodName}(\\Database\\IDatabaseConnection \$connection): bool {\n";

        // BUILD QUERY
        $methodBody .= "\t\t\$sql = \"DELETE FROM {$this->tableName} WHERE " . $this->sqlWhereColumns() . "\";";
        $methodBody .= "\n\n";

        $queryParams = $this->getColumnsAsMethodParams($primaryKeys, TRUE);

        $methodBody .= "\t\treturn \$connection->execute(\$sql, [{$queryParams}]);\n";

        // END BODY
        $methodBody .= "\t}";

        return $docComment . $methodBody;
    }
}
<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class Update
 *
 * @package Database\ClassGenerator\ClassParts
 */
class Update extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $docComment = "";
        $methodName = "update";
        $methodBody = "";

        $primaryKeys = array_filter(
            $this->tableColumns,
            function (array $column) {
                return $column['isPrimary'] === TRUE;
            });

        // COMMENT
        $docComment .= "\t/**\n";
        $docComment .= "\t * Update\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @param \\Database\\IDatabaseConnection \$connection\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return bool\n";
        $docComment .= "\t * @throws\n";
        $docComment .= "\t */\n";

        // BODY
        $methodBody .= "\tpublic function {$methodName}(\\Database\\IDatabaseConnection \$connection): bool {\n";

        // BUILD QUERY
        $methodBody .= "\t\t\$sql = \"UPDATE {$this->tableName} SET " . $this->sqlUpdateSetColumns() . "\n\t\t        WHERE " . $this->sqlWhereColumns() . "\";";
        $methodBody .= "\n\n";

        $queryParams = $this->getColumnsAsMethodParams(array_merge($this->tableColumns, $primaryKeys), TRUE);

        $methodBody .= "\t\treturn \$connection->execute(\$sql, [{$queryParams}]);";

        // END BODY
        $methodBody .= "\n\t}";

        return $docComment . $methodBody;
    }
}
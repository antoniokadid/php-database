<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodUpdatePart
 *
 * @package Database\Generators\Sections
 */
class MethodUpdateGeneratedPart extends GeneratedPart
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
                return $column['primary'] === TRUE;
            });

        // COMMENT
        $docComment .= TAB . "/**" . EOL;
        $docComment .= TAB . " * Update" . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return bool" . EOL;
        $docComment .= TAB . " * @throws" . EOL;
        $docComment .= TAB . " */" . EOL;

        // BODY
        $methodBody .= TAB . "public function {$methodName}(): bool {" . EOL;

        // BUILD QUERY
        $methodBody .= TAB . TAB . "\$sql = \"UPDATE {$this->tableName} SET ";

        $methodBody .= implode(", ",
                array_map(
                    function (array $column) {
                        return $column['colName'] . " = ?";
                    },
                    $this->tableColumns
                )
            ) . EOL;


        $methodBody .= TAB . "            WHERE " . implode(" AND ",
                array_map(
                    function (array $column) {
                        return $column['colName'] . " = ?";
                    },
                    $primaryKeys
                )
            ) . "\";" . EOL . EOL;


        $methodBody .= TAB . TAB . "return \$this->connection->execute(\$sql, [";

        // BUILD QUERY PARAMETERS

        $columns = array_merge($this->tableColumns, $primaryKeys);

        foreach ($columns as $column) {
            $propName = $column['propName'];
            $propType = $column['propType'];
            $colType = $column['colType'];
            $nullable = $column['nullable'];

            $methodBody .= EOL . TAB . TAB . TAB;

            if ($nullable === TRUE)
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
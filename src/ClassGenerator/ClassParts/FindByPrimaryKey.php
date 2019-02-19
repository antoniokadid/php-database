<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class FindByPrimaryKey
 *
 * @package Database\ClassGenerator\ClassParts
 */
class FindByPrimaryKey extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        // COMMENT
        $docComment =
            "\t/**\n" .
            "\t * Find a single {$this->className} object from database.\n" .
            "\t * \n" .
            "\t * @param \\Database\\IDatabaseConnection \$connection\n" .
            $this->getColumnsAsDocParams($this->getColumnsIncludedInPrimaryKey()) . "\n" .
            "\t * \n" .
            "\t * @return {$this->className}|NULL\n" .
            "\t * \n" .
            "\t * @throws\n" .
            "\t */\n";

        $pkeyCols = $this->getColumnsIncludedInPrimaryKey();


        // METHOD SIGNATURE
        $methodName = $this->getColumnsAsMethodName($pkeyCols);
        $methodParams = $this->getColumnsAsMethodSignatureParams($pkeyCols);

        $methodBody = "\tpublic static function findBy{$methodName}(\\Database\\IDatabaseConnection \$connection, {$methodParams}) : {$this->className} {\n\n";

        // BUILD QUERY
        $methodBody .= "\t\t\$sql = 'SELECT " . $this->sqlColumns() . "\n" .
                       "\t\t        FROM {$this->tableName}\n".
                       "\t\t        WHERE " . $this->sqlWhereColumns() . "';\n";
        $methodBody .= "\n";

        // CALL QUERY
        $queryParams = $this->getColumnsAsMethodParams($pkeyCols);

        $methodBody .= "\t\t\$record = \$connection->querySingle(\$sql, [{$queryParams}]);\n";
        $methodBody .= "\t\tif (\$record == NULL)\n";
        $methodBody .= "\t\t\treturn NULL;\n";
        $methodBody .= "\n";

        // GENERATE INSTANCE
        $methodBody .= "\t\t\$instance = new {$this->className}();\n\n";
        $methodBody .= $this->getColumnsForNewClassInstance($this->tableColumns, 'record', 'instance', 2) . "\n";
        $methodBody .= "\t\treturn \$instance;\n";
        $methodBody .= "\n";
        $methodBody .= "\t}";

        return $docComment . $methodBody;
    }
}

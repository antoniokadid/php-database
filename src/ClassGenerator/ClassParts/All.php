<?php

namespace Database\ClassGenerator\ClassParts;

use Database\ClassGenerator\ClassPart;

/**
 * Class All
 *
 * @package Database\ClassGenerator\ClassParts
 */
class All extends ClassPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        // COMMENT
        $docComment  = "\t/**\n";
        $docComment .= "\t * Get all {$this->className} objects from database.\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @param \\Database\\IDatabaseConnection \$connection\n";
        $docComment .= "\t * \n";
        $docComment .= "\t * @return {$this->className}[]\n";
        $docComment .= "\t * @throws\n";
        $docComment .= "\t */\n";

        $sql = sprintf("SELECT %s\n\t\t        FROM %s", $this->sqlColumns(), $this->tableName);

        // BODY
        $methodBody =  "\tpublic static function all(\\Database\\IDatabaseConnection \$connection) : array {\n";
        $methodBody .= "\t\t\$sql = '{$sql}';\n\n";
        $methodBody .= "\t\t\$result = \$connection->query(\$sql, []);\n\n";
        $methodBody .= "\t\treturn array_map(function(array \$record) {\n";
        $methodBody .= "\t\t\t\$instance = new {$this->className}();\n\n";
        $methodBody .= $this->getColumnsForNewClassInstance($this->tableColumns, 'record', 'instance', 3) . "\n";
        $methodBody .= "\t\t\treturn \$instance;\n";
        $methodBody .= "\t\t}, \$result);\n";
        $methodBody .= "\t}";

        return $docComment . $methodBody;
    }
}

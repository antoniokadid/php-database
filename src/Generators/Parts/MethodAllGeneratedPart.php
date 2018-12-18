<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodAllPart
 *
 * @package Database\Generators\Sections
 */
class MethodAllGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        // COMMENT
        $docComment = TAB . "/**" . EOL;
        $docComment .= TAB . " * Get all {$this->className} objects from database." . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @param IDatabaseConnection \$connection" . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return {$this->className}[]" . EOL;
        $docComment .= TAB . " * @throws" . EOL;
        $docComment .= TAB . " */" . EOL;

        $selectColumnList = implode(', ', array_map(function(array $column) { return $column['colName']; }, $this->tableColumns));
        $sql = "SELECT {$selectColumnList} " . EOL . TAB . TAB . "        FROM {$this->tableName}";

        // BODY
        $methodBody = TAB . "public static function all(IDatabaseConnection \$connection) : array {" . EOL;
        $methodBody .= TAB . TAB . "\$sql = '{$sql}';" . EOL . EOL;
        $methodBody .= TAB . TAB . "\$result = \$connection->query(\$sql, []);" . EOL . EOL;
        $methodBody .= TAB . TAB . "return array_map(function(array \$record) use (\$connection) {" . EOL;
        $methodBody .= TAB . TAB . TAB . "return {$this->className}::fromArray(\$record, \$connection);" . EOL;
        $methodBody .= TAB . TAB . "}, \$result);";
        $methodBody .= EOL . TAB . "}";

        return $docComment . $methodBody;
    }
}
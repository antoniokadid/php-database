<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

/**
 * Class MethodFindByPKeyPart
 *
 * @package Database\Generators\Sections
 */
class MethodFindByPKeyGeneratedPart extends GeneratedPart
{
    /**
     * @return string
     */
    public function process(): string
    {
        $primaryKeys = array_filter($this->tableColumns, function($column) { return $column['primary'] === TRUE; });
        if (count($primaryKeys) == 0)
            return '';

        $docParams = implode(EOL,
            array_map(function (array $column) {
                return TAB . " * @param " . trim($column['propType'] . " \${$column['propName']}");
            }, $primaryKeys));

        // COMMENT
        $docComment = TAB . "/**" . EOL;
        $docComment .= TAB . " * Find a single {$this->className} object from database." . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @param IDatabaseConnection \$connection" . EOL;
        $docComment .= $docParams . EOL;
        $docComment .= TAB . " * " . EOL;
        $docComment .= TAB . " * @return {$this->className}|NULL" . EOL;
        $docComment .= TAB . " * @throws" . EOL;
        $docComment .= TAB . " */" . EOL;

        // BODY PREP
        $methodName = "findBy" . implode("And", array_map(function (array $column) {
                return ucfirst($column['propName']);
            }, $primaryKeys));

        $methodParams = implode(", ", array_map(function (array $column) {
            return trim($column['propType'] . " \${$column['propName']}");
        }, $primaryKeys));

        $sqlWhereParams = implode(" AND ", array_map(function (array $column) {
            return "{$column['colName']} = ?";
        }, $primaryKeys));

        $preparedStmtParams = implode(", ", array_map(function (array $column) {
            $propName = $column['propName'];
            $propType = $column['propType'];
            $colType = $column['colType'];
            $nullable = $column['nullable'];

            if ($propType === '\DateTime')
            {
                if ($colType === 'DATE')
                    return "\${$propName}->format('Y-m-d')";
                else if ($colType === 'TIME')
                    return "\${$propName}->format('H:i:s')";
                else
                    return "\${$propName}->format('Y-m-d H:i:s')";
            }

            return "\${$propName}";
        }, $primaryKeys));

        $selectColumnList = implode(', ', array_map(function(array $column) { return $column['colName']; }, $this->tableColumns));
        $sql = "SELECT {$selectColumnList} " . EOL . TAB . TAB .
               "        FROM {$this->tableName} " . EOL . TAB . TAB .
               "        WHERE {$sqlWhereParams}";

        // BODY
        $methodBody = TAB . "public static function {$methodName}(IDatabaseConnection \$connection, {$methodParams}) : ?{$this->className} {" . EOL;
        $methodBody .= TAB . TAB . "\$sql = '{$sql}';" . EOL . EOL;
        $methodBody .= TAB . TAB . "\$record = \$connection->querySingle(\$sql, [{$preparedStmtParams}]);" . EOL . EOL;
        $methodBody .= TAB . TAB . "if (\$record == NULL)" . EOL .
                       TAB . TAB . "    return NULL;" . EOL .
                       TAB . TAB . "else" . EOL .
                       TAB . TAB . "    return {$this->className}::fromArray(\$record, \$connection);" . EOL;
        $methodBody .= TAB . "}";

        return $docComment . $methodBody;
    }
}
<?php

namespace Database\ClassGenerator;

/**
 * Class ClassPart
 *
 * @package Database\ClassGenerator
 */
abstract class ClassPart
{
    /** @var ClassGenerator */
    protected $generator;
    /** @var string */
    protected $tableName;
    /** @var array */
    protected $tableColumns;
    /** @var string */
    protected $className;

    /**
     * @param ClassGenerator $generator
     * @param string $tableName
     * @param array $tableColumns
     * @param string $className
     */
    public function init(ClassGenerator $generator, string $tableName, array $tableColumns, string $className): void
    {
        $this->generator = $generator;
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns;
        $this->className = $className;
    }

    /**
     * @return string
     */
    public abstract function process(): string;

    /**
     * @param int $countTab
     * @param int $countEol
     *
     * @return string
     */
    protected function tabEol(int $countTab = 1, int $countEol = 1)
    {
        return $this->tab($countTab) . $this->eol($countEol);
    }

    /**
     * @param int $count
     *
     * @return string
     */
    protected function tab(int $count = 1): string
    {
        return implode('', array_fill(0, $count, $this->generator->tab));
    }

    /**
     * @param int $count
     *
     * @return string
     */
    protected function eol(int $count = 1): string
    {
        return implode('', array_fill(0, $count, $this->generator->eol));
    }

    /**
     * Get the columns that are not part of primary key.
     *
     * @return array
     */
    protected function getColumnsNotIncludedInPrimaryKey(): array
    {
        return array_filter($this->tableColumns, function (array $column) {
            return $column['isPrimary'] !== TRUE;
        });
    }

    /**
     * Get comma-separated list of properties that are part of primary key.
     *
     * @param array $columns
     * @param bool $includeType If TRUE include the type of the property.
     *
     * @return string
     */
    protected function getColumnsAsMethodSignatureParams(array $columns, bool $includeType = TRUE): string
    {
        return implode(', ', array_map(function (array $column) use ($includeType) {
            if ($includeType === TRUE)
                return sprintf('%s $%s', $column['classPropertyType'], $column['classPropertyName']);
            else
                return sprintf('$%s', $column['classPropertyName']);
        }, $columns));
    }

    /**
     * Get the primary keys in a way that can be used in method names.
     *
     * @param array $columns
     * @param string $glue
     *
     * @return string
     */
    protected function getColumnsAsMethodName(array $columns, string $glue = 'And'): string
    {
        return implode($glue, array_map(function (array $column) {
            return ucfirst($column['classPropertyName']);
        }, $columns));
    }

    protected function getColumnsAsDocParams(array $columns): string
    {
        return implode(
            "\n",
            array_map(function (array $column) {
                return "\t * @param {$column['classPropertyType']} \${$column['classPropertyName']}";
            }, $columns));
    }

    /**
     * Convert columns to their property equivalent in order to consume their value, then join them in one string.
     *
     * @param array $columns
     * @param bool $useThis If true it will consider that instance properties are used.
     *
     * @return string
     */
    protected function getColumnsAsMethodParams(array $columns, bool $useThis = FALSE): string
    {
        return implode(
            ', ',
            array_map(function (array $column) use ($useThis) {
                $propName = $column['classPropertyName'];
                $propType = $column['classPropertyType'];
                $colType = $column['columnDataType'];
                $nullable = $column['isNullable'];

                $result = '';
                $context = ($useThis === TRUE) ? 'this->' : '';

                if ($nullable === TRUE)
                    $result .= "(\${$context}{$propName} != NULL) ? ";

                if ($propType === '\DateTime') {
                    $dateFormat = 'Y-m-d H:i:s';

                    if ($colType === 'DATE')
                        $dateFormat = 'Y-m-d';
                    else if ($colType === 'TIME')
                        $dateFormat = 'H:i:s';
                    else if ($colType === 'YEAR')
                        $dateFormat = 'Y';

                    $result .= "\${$context}{$propName}->format('{$dateFormat}')";
                } else
                    $result .= "\${$context}{$propName}";

                if ($nullable === TRUE)
                    $result .= " : NULL";

                return $result;

            }, $columns));
    }

    protected function getColumnsForInstanceAsArray(array $columns, int $tabCount = 0): string
    {
        $tabs = implode('', array_fill(0, $tabCount, "\t"));

        return implode(
            ",\n",
            array_map(function (array $column) use ($tabs) {
                $propName = $column['classPropertyName'];
                $propType = $column['classPropertyType'];
                $colName = $column['classPropertySerializedName'];
                $colType = $column['columnDataType'];
                $nullable = $column['isNullable'];

                $result = "{$tabs}'{$colName}' => ";

                if ($nullable === TRUE)
                    $result .= "(\$this->{$propName} != NULL) ? ";

                if ($propType === '\DateTime') {
                    $dateFormat = 'Y-m-d H:i:s';

                    if ($colType === 'DATE')
                        $dateFormat = 'Y-m-d';
                    else if ($colType === 'TIME')
                        $dateFormat = 'H:i:s';
                    else if ($colType === 'YEAR')
                        $dateFormat = 'Y';

                    $result .= "\$this->{$propName}->format('{$dateFormat}')";
                } else
                    $result .= "\$this->{$propName}";

                if ($nullable === TRUE)
                    $result .= " : NULL";

                return $result;

            }, $columns));
    }

    protected function getColumnsForNewClassInstanceFromSerialization(array $columns, string $recordVarName = 'record', string $instanceName = 'instance', int $tabCount = 0)
    {
        return $this->getColumnsForClassInstance('classPropertySerializedName', $columns, $recordVarName, $instanceName, $tabCount);
    }

    protected function getColumnsForNewClassInstance(array $columns, string $recordVarName = 'record', string $instanceName = 'instance', int $tabCount = 0)
    {
        return $this->getColumnsForClassInstance('columnName', $columns, $recordVarName, $instanceName, $tabCount);
    }

    private function getColumnsForClassInstance(string $columnNameField, array $columns, string $recordVarName = 'record', string $instanceName = 'instance', int $tabCount = 0)
    {
        $tabs = implode('', array_fill(0, $tabCount, "\t"));

        return array_reduce($columns,
            function($methodBody, $column) use ($columnNameField, $recordVarName, $instanceName, $tabs) {
                $propName = $column['classPropertyName'];
                $propType = $column['classPropertyType'];
                $colName = $column[$columnNameField];
                $colType = $column['columnDataType'];
                $nullable = $column['isNullable'];

                if ($propType === '\DateTime') {

                    $format = 'Y-m-d H:i:s';
                    if ($colType === 'DATE')
                        $format = 'Y-m-d';
                    else if ($colType === 'TIME')
                        $format = 'Η:i:s';
                    else if ($colType === 'YEAR')
                        $format = 'Y';

                    $methodBody .= "{$tabs}\$value = \\DateTime::createFromFormat('{$format}', \${$recordVarName}['{$colName}'], new \\DateTimeZone('" . $this->generator->timezone . "'));\n";
                    $methodBody .= "{$tabs}\${$instanceName}->{$propName} = (\$value === FALSE) ? NULL : \$value;\n";
                } else {
                    $methodBody .= "{$tabs}\${$instanceName}->{$propName} = ";

                    if ($nullable === TRUE)
                        $methodBody .= "(\${$recordVarName}['" . $colName . "'] != NULL) ? ";

                    if ($propType == 'bool')
                        $methodBody .= "boolval(\${$recordVarName}['" . $colName . "'])";
                    else if ($propType == 'int')
                        $methodBody .= "intval(\${$recordVarName}['" . $colName . "'])";
                    else if ($propType == 'float')
                        $methodBody .= "floatval(\${$recordVarName}['" . $colName . "'])";
                    else if ($propType == 'string')
                        $methodBody .= "strval(\${$recordVarName}['" . $colName . "'])";
                    else
                        $methodBody .= "\${$recordVarName}['" . $colName . "']";

                    $methodBody .= ($nullable === TRUE) ? " : NULL;\n" : ";\n";
                }

                return $methodBody;
            }, '');
    }

    /**
     * Get column names as comma-separated values.
     *
     * @return string
     */
    protected function sqlColumns(): string
    {
        return implode(
            ', ',
            array_map(function (array $column) {
                return $this->generator->formatName(ClassGenerator::FORMAT_COLUMN_NAME, $column['columnName']);
            }, $this->tableColumns));
    }

    protected function sqlAddColumns()
    {
        return implode(', ', array_fill(0, count($this->tableColumns), '?'));
    }

    protected function sqlUpdateSetColumns()
    {
        return implode(
            ', ',
            array_map(function (array $column) {
                return $this->generator->formatName(ClassGenerator::FORMAT_COLUMN_NAME, $column['columnName']) . ' = ?';
            }, $this->tableColumns));
    }

    protected function sqlWhereColumns()
    {
        return implode(
            ' AND ',
            array_map(function (array $column) {
                return $this->generator->formatName(ClassGenerator::FORMAT_COLUMN_NAME, $column['columnName']) . ' = ?';
            }, $this->getColumnsIncludedInPrimaryKey()));
    }

    /**
     * Get the primary key columns.
     *
     * @return array
     */
    protected function getColumnsIncludedInPrimaryKey(): array
    {
        return array_filter($this->tableColumns, function (array $column) {
            return $column['isPrimary'] === TRUE;
        });
    }
}

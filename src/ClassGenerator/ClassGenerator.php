<?php

namespace Database\ClassGenerator;

use Database\IDatabaseConnection;

/**
 * Class PdoMySqlTableToClassGenerator
 *
 * @package Database\Generators
 */
class ClassGenerator
{
    const FORMAT_COLUMN_NAME = 1;
    const FORMAT_TABLE_NAME = 2;
    const FORMAT_CLASS_NAME = 4;
    const FORMAT_PROPERTY_NAME = 8;
    const FORMAT_SERIALIZED_PROPERTY_NAME = 16;

    /** @var IDatabaseConnection */
    private $_conn;
    /** @var string */
    private $_dbName;
    /** @var Callable */
    private $_nameFormatter;
    /** @var Callable */
    private $_typeMapper;
    /** @var ClassPart[] */
    private $_globalParts = [];
    /** @var ClassPart[] */
    private $_tableParts = [];

    /**
     * MySqlModelGenerator constructor.
     *
     * @param IDatabaseConnection $connection
     * @param string $dbName
     */
    public function __construct(IDatabaseConnection $connection, string $dbName)
    {
        $this->_conn = $connection;
        $this->_dbName = $dbName;
    }

    /** @var string */
    public $namespace;
    /** @var string */
    public $tab = '    ';
    /** @var string */
    public $eol = PHP_EOL;
    /** @var string */
    public $timezone = 'UTC';

    /**
     * @return IDatabaseConnection
     */
    public function getConn(): IDatabaseConnection
    {
        return $this->_conn;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->_dbName;
    }

    /**
     * @param ClassPart $part
     * @param string $tableName
     */
    public function addPart(ClassPart $part, string $tableName = ''): void
    {
        if (trim($tableName) === '')
            $this->_globalParts[] = $part;
        else {
            if (!array_key_exists($tableName, $this->_tableParts))
                $this->_tableParts[$tableName] = [];

            $this->_tableParts[$tableName][] = $part;
        }
    }

    /**
     * Set a handler to format names.
     *
     * @param callable $formatter
     */
    public function setNameFormatter(callable $formatter)
    {
        $this->_nameFormatter = $formatter;
    }

    /**
     * Set a handler to map unsupported MySql types to PHP types.
     *
     * @param callable $typeMapper
     */
    public function setUnknownMySqlToPhpTypeMapper(callable $typeMapper)
    {
        $this->_typeMapper = $typeMapper;
    }

    /**
     * @param string $outputPath
     *
     * @throws \Database\DatabaseException
     */
    public function generate(string $outputPath): void
    {
        $this->prepareDefaults();

        $tables = $this->loadTables();

        foreach ($tables as $table)
            $this->generateClass($table, $outputPath);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function graveIt(string $value): string
    {
        return "`{$value}`";
    }

    private function prepareDefaults(): void
    {
        if (!is_string($this->timezone) || trim($this->timezone) === '')
            $this->timezone = date_default_timezone_get();

        if (!is_string($this->eol))
            $this->eol = PHP_EOL;

        if (!is_string($this->tab))
            $this->tab = "\t";

        $this->namespace = is_string($this->namespace) && trim($this->namespace) !== '' ?
            sprintf('namespace %s;', trim($this->namespace)) . $this->eol . $this->eol :
            '';
    }

    /**
     * @return array
     *
     * @throws \Database\DatabaseException
     */
    private function loadTables(): array
    {
        $sql = 'SELECT `table_name` 
                FROM `information_schema`.`tables` 
                WHERE `table_schema` = ?';

        return array_map(
            function ($tableInfo) {
                return $tableInfo['TABLE_NAME'];
            },
            $this->_conn
                ->query($sql, [$this->_dbName]));
    }

    /**
     * @param string $tableName
     * @param string $outputPath
     *
     * @throws \Database\DatabaseException
     */
    private function generateClass(string $tableName, string $outputPath): void
    {
        $className = $this->formatName(self::FORMAT_CLASS_NAME, $tableName);
        $tableColumns = $this->processTableColumns($tableName);

        $output = "<?php" . $this->eol . $this->eol;
        $output .= $this->namespace;
        $output .= "class {$className}" . $this->eol;
        $output .= '{';

        $parts = array_key_exists($tableName, $this->_tableParts) ?
            array_merge($this->_globalParts, $this->_tableParts[$tableName]) :
            $this->_globalParts;

        /** @var ClassPart[] $parts */
        foreach ($parts as $part) {

            $part->init($this, $tableName, $tableColumns, $className);
            $result = $part->process();

            if (strlen($result) == 0)
                continue;

            $output .= $this->eol . $result . $this->eol;
        }

        $output .= '}';

        $output = str_replace("\t", $this->tab, $output);
        $output = str_replace("\n", $this->eol, $output);

        file_put_contents("{$outputPath}/{$className}.php", $output);
    }

    /**
     * @param int $type
     * @param string $name
     *
     * @return string
     */
    public function formatName(int $type, string $name): string
    {
        if (!is_callable($this->_nameFormatter))
            return $name;

        return call_user_func_array($this->_nameFormatter, [$type, $name]);
    }

    /**
     * @param array $column
     *
     * @return string|null
     */
    private function mapType(array $column): ?string
    {
        if (!is_callable($this->_typeMapper))
            return NULL;

        $result = call_user_func_array($this->_typeMapper, [$column]);

        return (is_null($result) || !is_string($result)) ? NULL : $result;
    }

    /**
     * @param string $tableName
     *
     * @return array
     *
     * @throws \Database\DatabaseException
     */
    private function processTableColumns(string $tableName): array
    {
        $sql = 'SELECT *
                FROM `information_schema`.`columns`
                WHERE `table_schema` = ? AND 
                      `table_name` = ?';
        $columns = $this->_conn->query($sql, [$this->_dbName, $tableName]);

        $tableFormattedName = $this->formatName(self::FORMAT_TABLE_NAME, $tableName);
        $classFormattedName = $this->formatName(self::FORMAT_CLASS_NAME, $tableName);

        return array_map(function ($column) use ($tableFormattedName, $classFormattedName) {
            return [
                'className' => $classFormattedName,
                'classPropertyName' => $this->formatName(self::FORMAT_PROPERTY_NAME, $column['COLUMN_NAME']),
                'classPropertyType' => $this->getMappedColumnType($column),
                'classPropertySerializedName' => $this->formatName(self::FORMAT_SERIALIZED_PROPERTY_NAME, $column['COLUMN_NAME']),
                'tableName' => $tableFormattedName,
                'columnName' => $column['COLUMN_NAME'],
                'columnType' => strtoupper($column['COLUMN_TYPE']),
                'columnDataType' => strtoupper($column['DATA_TYPE']),
                'isNullable' => $column['IS_NULLABLE'] === 'YES',
                'isPrimary' => $column['COLUMN_KEY'] === 'PRI'
            ];
        }, $columns);
    }

    /**
     * @param array $column
     * @param bool $includeNullableMark
     *
     * @return string|null
     */
    private function getMappedColumnType(array $column, bool $includeNullableMark = TRUE): ?string
    {
        $dataType = strtoupper($column['DATA_TYPE']);
        $columnType = strtoupper($column['COLUMN_TYPE']); // Includes length
        $nullable = $column['IS_NULLABLE'] === 'YES';

        switch ($dataType) {
            case 'TINYINT':
                $type = ($columnType === 'TINYINT(1)') ? 'bool' : 'int';
                break;
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'INT':
            case 'BIGINT':
                $type = 'int';
                break;
            case 'FLOAT':
            case 'DOUBLE':
            case 'DECIMAL':
                $type = 'float';
                break;
            case 'CHAR':
            case 'VARCHAR':
            case 'TINYTEXT':
            case 'TEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
                $type = 'string';
                break;
            case 'DATE':
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'TIME':
            case 'YEAR':
                return '\DateTime'; // Don't include null operator
            default:
                $type = $this->mapType($column);
                break;
        }

        if ($type == NULL)
            return NULL;

        return ($includeNullableMark && $nullable) ? "?{$type}" : $type;
    }
}

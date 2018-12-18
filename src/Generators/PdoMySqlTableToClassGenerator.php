<?php

namespace Database\Generators;

require_once __DIR__ . '/Definitions.php';

use Database\Generators\Parts\ClassGeneratedPart;
use Database\IDatabaseConnection;

/**
 * Class PdoMySqlTableToClassGenerator
 *
 * @package Database\Generators
 */
class PdoMySqlTableToClassGenerator
{
    /** @var IDatabaseConnection */
    private $conn;
    /** @var string */
    private $dbName;

    /**
     * MySqlModelGenerator constructor.
     *
     * @param IDatabaseConnection $connection
     * @param string $dbName
     */
    public function __construct(IDatabaseConnection $connection, string $dbName)
    {
        $this->conn = $connection;
        $this->dbName = $dbName;
    }

    /** @var Callable */
    public static $formatName;

    /**
     * @param int $type
     * @param string $name
     *
     * @return string
     */
    private static function formatName(int $type, string $name): string
    {
        if (!is_callable(self::$formatName))
            return $name;

        return call_user_func_array(self::$formatName, [$type, $name]);
    }

    /**
     * @param array $column
     *
     * @return string|null
     */
    private static function getMappedColumnType(array $column): ?string
    {
        foreach (TYPE_MAP as $pattern => $type) {
            if (preg_match($pattern, $column['DATA_TYPE']) == FALSE)
                continue;

            if ($column['DATA_TYPE'] === 'TINYINT' && $column['COLUMN_TYPE'] === 'TINYINT(1)')
                $type = 'bool';

            if ($type === '\DateTime')
                return $type;

            return $column['IS_NULLABLE'] === 'YES' ? "?{$type}" : $type;
        }

        return NULL;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private static function graveIt(string $value): string
    {
        return "`{$value}`";
    }

    /**
     * @param string $outputPath
     *
     * @throws \Database\DatabaseException
     */
    public function generate(string $outputPath): void
    {
        $sql = 'SELECT `table_name` 
                FROM `information_schema`.`tables` 
                WHERE `table_schema` = ?';

        $tables = array_map(
            function ($tableInfo) {
                return $tableInfo['TABLE_NAME'];
            },
            $this->conn->query($sql, [$this->dbName]));

        foreach ($tables as $table) {
            $className = self::formatName(FMT_NAME_CLASS, $table);
            $classCode = $this->processTable($table);
            $path = "{$outputPath}/{$className}.php";

            file_put_contents($path, "<?php" . EOL . EOL . $classCode);
            chmod($path, 0777);
        }
    }

    /**
     * @param string $tableName
     *
     * @return array
     *
     * @throws \Database\DatabaseException
     */
    private function processProperties(string $tableName): array
    {
        $sql = 'SELECT *
                FROM `information_schema`.`columns`
                WHERE `table_schema` = ? AND 
                      `table_name` = ?';
        $columns = $this->conn->query($sql, [$this->dbName, $tableName]);

        return array_map(function ($column) {
            return [
                'propName' => self::formatName(FMT_NAME_PROPERTY, $column['COLUMN_NAME']),
                'propType' => $this->getMappedColumnType($column),
                'colName' => self::graveIt($column['COLUMN_NAME']),
                'nullable' => $column['IS_NULLABLE'] === 'YES',
                'primary' => $column['COLUMN_KEY'] === 'PRI'
            ];
        }, $columns);
    }

    /**
     * @param string $tableName
     *
     * @return string
     *
     * @throws \Database\DatabaseException
     */
    private function processTable(string $tableName): string
    {
        $className = self::formatName(FMT_NAME_CLASS, $tableName);
        $tableColumns = $this->processProperties($tableName);

        $classPart = new ClassGeneratedPart();
        $classPart->init($this->conn, $this->dbName, self::graveIt($tableName), $tableColumns, $className);

        return $classPart->process();
    }
}
<?php

namespace Database\Generators;

use Database\IDatabaseConnection;

/**
 * Class GeneratedPart
 *
 * @package Database\Generators
 */
abstract class GeneratedPart
{
    /** @var IDatabaseConnection */
    protected $connection;
    /** @var string */
    protected $dbName;
    /** @var string */
    protected $tableName;
    /** @var array */
    protected $tableColumns;
    /** @var string */
    protected $className;

    /**
     * @param IDatabaseConnection $connection
     * @param string $dbName
     * @param string $tableName
     * @param string $className
     * @param array $tableColumns
     */
    public function init(IDatabaseConnection $connection, string $dbName, string $tableName, array $tableColumns, string $className)
    {
        $this->connection = $connection;
        $this->dbName = $dbName;
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns;
        $this->className = $className;
    }

    /**
     * @return string
     */
    public abstract function process(): string;
}
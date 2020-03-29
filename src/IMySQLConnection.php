<?php

namespace AntonioKadid\WAPPKitCore\Database\MySQL;

use AntonioKadid\WAPPKitCore\Database\MySQL\Exceptions\MySQLException;

/**
 * Interface IMySQLConnection
 *
 * @package AntonioKadid\WAPPKitCore\Database\MySQL
 */
interface IMySQLConnection
{
    /**
     * Commit the active transaction.
     *
     * @return bool
     *
     * @throws MySQLException
     */
    function commit(): bool;

    /**
     * Execute a DELETE, INSERT or UPDATE query.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return int The number of affected rows.
     *
     * @throws MySQLException
     */
    function execute(string $sql, array $params = []): int;

    /**
     * Execute a SELECT query.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return array
     *
     * @throws MySQLException
     */
    function query(string $sql, array $params = []): array;


    /**
     * Rollback the active transaction.
     *
     * @return bool
     */
    function rollback(): bool;
}
<?php

namespace AntonioKadid\MySql;

/**
 * Interface IDatabaseConnection
 *
 * @package AntonioKadid\MySql
 */
interface IDatabaseConnection
{
    /**
     * Commit the active transaction.
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function commit(): bool;

    /**
     * Execute a DELETE, INSERT or UPDATE query.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return int The number of affected rows.
     *
     * @throws DatabaseException
     */
    public function execute(string $sql, array $params = array()): int;

    /**
     * Execute a SELECT query.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return array
     *
     * @throws DatabaseException
     */
    public function query(string $sql, array $params = []): array;


    /**
     * Rollback the active transaction.
     *
     * @return bool
     */
    public function rollback(): bool;
}
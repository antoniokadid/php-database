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
     * @return bool
     *
     * @throws DatabaseException
     */
    public function commit(): bool;

    /**
     * @param string $sql
     * @param array $params
     *
     * @return int
     *
     * @throws DatabaseException
     */
    public function execute(string $sql, array $params = array()): int;

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     *
     * @throws DatabaseException
     */
    public function query(string $sql, array $params = array()): array;

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array|NULL
     *
     * @throws DatabaseException
     */
    public function querySingle(string $sql, array $params = array()): ?array;

    /**
     * Rollback the active transaction.
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function rollback(): bool;
}

<?php

namespace AntonioKadid\MySql;

use mysqli;
use mysqli_stmt;

/**
 * Class MySQLiConnection
 *
 * @package AntonioKadid\MySql
 */
class MySQLiConnection implements IDatabaseConnection
{
    /** @var  mysqli $_mysqli */
    private $_mysqli;

    /**
     * MySQLiConnection constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $dbName
     * @param string $username
     * @param string $password
     * @param string $encoding
     *
     * @throws DatabaseConnectionException
     */
    public function __construct(string $host,
                                int $port,
                                string $dbName,
                                string $username,
                                string $password,
                                string $encoding = 'UTF8')
    {
        $this->_mysqli = new mysqli($host, $username, $password, $dbName, $port);
        if ($this->_mysqli->connect_errno !== 0)
            throw new DatabaseConnectionException(sprintf('Cannot establish connection with the database: %s', $this->_mysqli->connect_error));

        if ($this->_mysqli->set_charset($encoding) !== TRUE)
            throw new DatabaseConnectionException('Cannot set character set.');

        if ($this->_mysqli->autocommit(FALSE) !== TRUE)
            throw new DatabaseConnectionException('Cannot disable auto-commit.');

        if ($this->_mysqli->begin_transaction() !== TRUE)
            throw new DatabaseConnectionException('Cannot initiate transaction.');
    }

    public function __destruct()
    {
        $this->rollback();
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        if ($this->_mysqli == NULL)
            throw new DatabaseException('Database connection not initialized.');

        if ($this->_mysqli->ping() !== TRUE)
            throw new DatabaseException('Database connection not active.');

        if ($this->_mysqli->commit() !== TRUE)
            throw new DatabaseException($this->_mysqli->error);

        $this->_mysqli->close();
        $this->_mysqli = NULL;

        return TRUE;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql, array $params = []): int
    {
        if ($this->_mysqli == NULL)
            throw new DatabaseException('Database connection not initialized.');

        if ($this->_mysqli->ping() !== TRUE)
            throw new DatabaseException('Database connection not active.');

        $preparedStatement = $this->_mysqli->prepare($sql);
        if ($preparedStatement === FALSE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        if ($this->bindParameters($preparedStatement, $params) !== TRUE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        if ($preparedStatement->execute() !== TRUE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        return $preparedStatement->affected_rows;
    }

    /**
     * @inheritDoc
     */
    public function query(string $sql, array $params = []): array
    {
        if ($this->_mysqli == NULL)
            throw new DatabaseException('Database connection not initialized.');

        if ($this->_mysqli->ping() !== TRUE)
            throw new DatabaseException('Database connection not active.');

        $preparedStatement = $this->_mysqli->prepare($sql);
        if ($preparedStatement === FALSE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        if ($this->bindParameters($preparedStatement, $params) !== TRUE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        if ($preparedStatement->execute() !== TRUE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        $preparedResult = $preparedStatement->get_result();
        if ($preparedResult === FALSE)
            throw new DatabaseException($this->_mysqli->error, $sql, $params);

        $result = [];
        while (($record = $preparedResult->fetch_assoc()) != NULL)
            $result[] = $record;

        $preparedResult->free();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        if ($this->_mysqli == NULL)
            return FALSE;

        if ($this->_mysqli->ping() !== TRUE)
            return FALSE;

        $result = $this->_mysqli->rollback();

        $this->_mysqli->close();
        $this->_mysqli = NULL;

        return $result;
    }

    /**
     * @param mysqli_stmt $stmt
     * @param array       $params
     *
     * @return bool
     */
    private function bindParameters(mysqli_stmt $stmt, array $params): bool
    {
        if (empty($params))
            return TRUE;

        $type = '';
        foreach ($params as $value) {
            if (is_string($value) || is_null($value))
                $type .= 's';
            else if (is_int($value) || is_bool($value))
                $type .= 'i';
            else if (is_double($value))
                $type .= 'd';
            else
                $type .= 'b';
        }

        return $stmt->bind_param($type, ...$params);
    }
}
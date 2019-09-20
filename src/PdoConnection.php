<?php

namespace AntonioKadid\MySql;

use PDO;
use PDOException;

/**
 * Class PdoConnection
 *
 * @package AntonioKadid\MySql
 */
class PdoConnection
{
    /** @var PDO */
    private $_pdo;

    /**
     * PdoConnection constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $dbName
     * @param string $username
     * @param string $password
     * @param string $encoding
     *
     * @throws DatabaseException
     */
    public function __construct(string $host,
                                int $port,
                                string $dbName,
                                string $username,
                                string $password,
                                string $encoding = 'utf8')
    {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $encoding);
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_AUTOCOMMIT => FALSE
        ];

        try {
            $this->_pdo = new PDO($dsn, $username, $password, $options);
            $this->_pdo->beginTransaction();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to establish connection with database.', '', [], 0, $pdoEx);
        }
    }

    /**
     * @throws DatabaseException
     */
    public function __destruct()
    {
        $this->rollback();
        $this->_pdo = NULL;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        if ($this->_pdo == NULL)
            throw new DatabaseException('Database connection not initialized.');

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new DatabaseException('Not in transaction.');

        if (!$this->_pdo->commit())
            throw new DatabaseException($this->_pdo->errorInfo()[2]);

        return TRUE;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql, array $params = array()): int
    {
        if ($this->_pdo == NULL)
            throw new DatabaseException('Database connection not initialized.', $sql, $params);

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new DatabaseException('Not in transaction.', $sql, $params);

        $preparedStatement = $this->_pdo->prepare($sql);
        if ($preparedStatement === FALSE)
            throw new DatabaseException($this->_pdo->errorInfo()[2], $sql, $params);

        $result = $preparedStatement->execute($params);
        if ($result === FALSE)
            throw new DatabaseException($this->_pdo->errorInfo()[2], $sql, $params);

        return $preparedStatement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function query(string $sql, array $params = []): array
    {
        if ($this->_pdo == NULL)
            throw new DatabaseException('Database connection not initialized.', $sql, $params);

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new DatabaseException('Not in transaction.', $sql, $params);

        $preparedStatement = $this->_pdo->prepare($sql);
        if ($preparedStatement === FALSE)
            throw new DatabaseException($this->_pdo->errorInfo()[2], $sql, $params);

        $result = $preparedStatement->execute($params);
        if ($result === FALSE)
            throw new DatabaseException($this->_pdo->errorInfo()[2], $sql, $params);

        $records = $preparedStatement->fetchAll();
        if ($records === FALSE)
            throw new DatabaseException($this->_pdo->errorInfo()[2], $sql, $params);

        return $records;
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        if ($this->_pdo == NULL)
            throw new DatabaseException('Database connection not initialized.');

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new DatabaseException('Not in transaction.');

        if (!$this->_pdo->rollBack())
            throw new DatabaseException($this->_pdo->errorInfo()[2]);

        return TRUE;
    }
}
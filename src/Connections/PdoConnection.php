<?php

namespace AntonioKadid\WAPPKitCore\Database\MySQL\Connections;

use AntonioKadid\WAPPKitCore\Database\MySQL\Exceptions\MySQLException;
use AntonioKadid\WAPPKitCore\Database\MySQL\IMySQLConnection;
use PDO;
use PDOException;

/**
 * Class PdoConnection
 *
 * @package AntonioKadid\WAPPKitCore\Database\MySQL\Connections
 */
class PdoConnection implements IMySQLConnection
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
     * @throws MySQLException
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
            throw new MySQLException($pdoEx->getMessage(), '', [], $pdoEx->getCode(), $pdoEx);
        }
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
        if ($this->_pdo == NULL)
            throw new MySQLException('Database connection not initialized.');

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new MySQLException('Not in transaction.');

        if (!$this->_pdo->commit())
            throw new MySQLException($this->_pdo->errorInfo()[2], '', [], $this->_pdo->errorInfo()[1]);

        $this->_pdo = NULL;

        return TRUE;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql, array $params = array()): int
    {
        if ($this->_pdo == NULL)
            throw new MySQLException('Database connection not initialized.', $sql, $params);

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new MySQLException('Not in transaction.', $sql, $params);

        $statement = $this->_pdo->prepare($sql);
        if ($statement === FALSE)
            throw new MySQLException($this->_pdo->errorInfo()[2], $sql, $params, $this->_pdo->errorInfo()[1]);

        $result = $statement->execute($params);
        if ($result === FALSE)
            throw new MySQLException($statement->errorInfo()[2], $sql, $params, $statement->errorInfo()[1]);

        return $statement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function query(string $sql, array $params = []): array
    {
        if ($this->_pdo == NULL)
            throw new MySQLException('Database connection not initialized.', $sql, $params);

        if ($this->_pdo->inTransaction() !== TRUE)
            throw new MySQLException('Not in transaction.', $sql, $params);

        $statement = $this->_pdo->prepare($sql);
        if ($statement === FALSE)
            throw new MySQLException($this->_pdo->errorInfo()[2], $sql, $params, $this->_pdo->errorInfo()[1]);

        $result = $statement->execute($params);
        if ($result === FALSE)
            throw new MySQLException($statement->errorInfo()[2], $sql, $params, $statement->errorInfo()[1]);

        $records = $statement->fetchAll();
        if ($records === FALSE)
            throw new MySQLException($statement->errorInfo()[2], $sql, $params, $statement->errorInfo()[1]);

        return $records;
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        if ($this->_pdo == NULL)
            return FALSE;

        if ($this->_pdo->inTransaction() !== TRUE)
            return FALSE;

        $result = $this->_pdo->rollBack();

        $this->_pdo = NULL;

        return $result;
    }
}
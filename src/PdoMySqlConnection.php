<?php

namespace Database;

use PDO;
use PDOException;

/**
 * Class PdoMySqlConnection
 *
 * @package Database
 */
class PdoMySqlConnection implements IDatabaseConnection
{
    /** @var PDO */
    private $pdo;

    /**
     * PdoMySqlConnection constructor.
     *
     * @param string $host
     * @param int $port
     * @param $dbName
     * @param string $username
     * @param string $password
     * @param string $encoding
     * @param array $options
     *
     * @throws DatabaseException
     */
    public function __construct(string $host,
                                 int $port,
                                 string $dbName,
                                 string $username,
                                 string $password,
                                 string $encoding,
                                 array $options = [])
    {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $encoding);
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_CASE => PDO::CASE_UPPER
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $opt);
            $this->pdo->beginTransaction();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to establish connection with database.', 0, $pdoEx);
        }
    }

    /**
     * PdoMySqlConnection destructor.
     */
    public function __destruct()
    {
        if ($this->pdo == NULL)
            return;

        $this->rollback();

        $this->pdo = NULL;
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        if ($this->pdo == NULL)
            return FALSE;

        if (!$this->pdo->inTransaction())
            return FALSE;

        return $this->pdo->commit();
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return int
     *
     * @throws DatabaseException
     */
    public function execute(string $sql, array $params = array()): int
    {
        if ($this->pdo == NULL)
            throw new DatabaseException('Database connection not initialized.');

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt === FALSE)
                throw new DatabaseException('Unable to prepare sql query in execute.');

            if ($stmt->execute($params) === FALSE)
                throw new DatabaseException('Unable to execute sql query in execute.');

            return $stmt->rowCount();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to execute query.', 0, $pdoEx);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     * @throws DatabaseException
     */
    public function query(string $sql, array $params = array()): array
    {
        if ($this->pdo == NULL)
            throw new DatabaseException('Database connection not initialized.');

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt === FALSE)
                throw new DatabaseException('Unable to prepare sql query in query.');

            if ($stmt->execute($params) === FALSE)
                throw new DatabaseException('Unable to execute sql query in query.');

            $result = $stmt->fetchAll();
            if ($result === FALSE)
                throw new DatabaseException('Unable to fetch data in query');

            return $result;
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to execute query.', 0, $pdoEx);
        }
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array|NULL
     * @throws DatabaseException
     */
    public function querySingle(string $sql, array $params = array()): ?array
    {
        if ($this->pdo == NULL)
            throw new DatabaseException('Database connection not initialized.');

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt === FALSE)
                throw new DatabaseException('Unable to prepare sql query in query single.');

            if ($stmt->execute($params) === FALSE)
                throw new DatabaseException('Unable to execute sql query in query single.');

            $result = $stmt->fetch();
            if ($result === FALSE)
                throw new DatabaseException('Unable to fetch data in query single.');

            return $result;
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to execute query single.', 0, $pdoEx);
        }
    }

    /**
     * Rollback the active transaction.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        if ($this->pdo == NULL)
            return FALSE;

        if (!$this->pdo->inTransaction())
            return FALSE;

        return $this->pdo->rollBack();
    }
}
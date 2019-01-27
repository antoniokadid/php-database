<?php

namespace Database;

use PDO;
use PDOException;

/**
 * Class PdoConnection
 *
 * @package Database
 */
class PdoConnection implements IDatabaseConnection
{
    /** @var PDO */
    private $pdo;

    /**
     * PdoConnection constructor.
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
        $opt = array_replace([
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ], $options);

        try {
            $this->pdo = new PDO($dsn, $username, $password, $opt);
            $this->pdo->beginTransaction();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to establish connection with database.', 0, $pdoEx);
        }
    }

    /**
     * PdoConnection destructor.
     */
    public function __destruct()
    {
        try {
            $this->rollback();
        } catch (DatabaseException $e) {
        }

        $this->pdo = NULL;
    }

    /**
     * @return bool
     *
     * @throws DatabaseException
     */
    public function commit(): bool
    {
        if ($this->pdo == NULL)
            return FALSE;

        try {
            if (!$this->pdo->inTransaction())
                return FALSE;

            return $this->pdo->commit();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to commit.', 0, $pdoEx);
        }
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
            return 0;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

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
     *
     * @throws DatabaseException
     */
    public function query(string $sql, array $params = array()): array
    {
        if ($this->pdo == NULL)
            return [];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
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
            return NULL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch();
        
            if ($result === FALSE || !is_array($result) || empty($result))
                return NULL;

            return $result;
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to execute query single.', 0, $pdoEx);
        }
    }

    /**
     * Rollback the active transaction.
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function rollback(): bool
    {
        if ($this->pdo == NULL)
            return FALSE;

        try {
            if (!$this->pdo->inTransaction())
                return FALSE;

            return $this->pdo->rollBack();
        } catch (PDOException $pdoEx) {
            throw new DatabaseException('Unable to rollback.', 0, $pdoEx);
        }
    }
}
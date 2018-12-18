<?php

use Database\IDatabaseConnection;

class HelloWorld
{
    /** @var IDatabaseConnection */
    private $connection;

    /**
     * HelloWorld constructor.
     * 
     * @param IDatabaseConnection|NULL $connection
     */
    public function __construct(IDatabaseConnection $connection = NULL) {
        $this->connection = $connection;
    }

    /** @var string */
    public $id;
    /** @var string */
    public $name;

    /**
     * Get all HelloWorld objects from database.
     * 
     * @param IDatabaseConnection $connection
     * 
     * @return HelloWorld[]
     * @throws
     */
    public static function all(IDatabaseConnection $connection) : array {
        $sql = 'SELECT `id`, `name` 
                FROM `HelloWorld`';

        $result = $connection->query($sql, []);

        return array_map(function(array $record) use ($connection) {
            return HelloWorld::fromArray($record, $connection);
        }, $result);
    }

    /**
     * Find a single HelloWorld object from database.
     * 
     * @param IDatabaseConnection $connection
     * @param string $id
     * 
     * @return HelloWorld|NULL
     * @throws
     */
    public static function findById(IDatabaseConnection $connection, string $id) : ?HelloWorld {
        $sql = 'SELECT `id`, `name` 
                FROM `HelloWorld` 
                WHERE `id` = ?';

        $record = $connection->querySingle($sql, [$id]);

        if ($record == NULL)
            return NULL;
        else
            return HelloWorld::fromArray($record, $connection);
    }

    /**
     * Convert an array into an instance of HelloWorld.
     * 
     * @param array $array
     * @param IDatabaseConnection|NULL $connection
     * 
     * @return HelloWorld
     */
    public static function fromArray(array $array, IDatabaseConnection $connection = NULL): HelloWorld {
        $result = new HelloWorld($connection);

        $result->id = strval($array['id']);
        $result->name = strval($array['name']);

        return $result;
    }

    /**
     * Add
     * 
     * @return bool
     * @throws
     */
    public function add(): bool {
        $sql = "INSERT INTO `HelloWorld` (`id`, `name`) 
                VALUES (?, ?)";

        return $this->connection->execute($sql, [
            $this->id,
            $this->name,
        ]);
    }

    /**
     * Convert an instance of HelloWorld into an array.
     * 
     * @return array
     */
    public function asArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * Delete
     * 
     * @return bool
     * @throws
     */
    public function delete(): bool {
        $sql = "DELETE FROM `HelloWorld` 
                WHERE `id` = ?";

        return $this->connection->execute($sql, [$this->id]);
    }

    /**
     * Update
     * 
     * @return bool
     * @throws
     */
    public function update(): bool {
        $sql = "UPDATE `HelloWorld` SET `id` = ?, `name` = ?
                WHERE `id` = ?";

        return $this->connection->execute($sql, [
            $this->id,
            $this->name,
            $this->id,
        ]);
    }
}
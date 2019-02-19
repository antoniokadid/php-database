<?php

namespace Models;

class HelloWorld
{
    /** @var string */
    public $id;
    /** @var string */
    public $name;

    /**
     * Get all HelloWorld objects from database.
     *
     * @param \Database\IDatabaseConnection $connection
     *
     * @return HelloWorld[]
     * @throws
     */
    public static function all(\Database\IDatabaseConnection $connection) : array {
        $sql = 'SELECT `id`, `name`
                FROM HelloWorld';

        $result = $connection->query($sql, []);

        return array_map(function(array $record) {
            $instance = new HelloWorld();

            $instance->id = strval($record['id']);
            $instance->name = strval($record['name']);

            return $instance;
        }, $result);
    }

    /**
     * Find a single HelloWorld object from database.
     *
     * @param \Database\IDatabaseConnection $connection
     * @param string $id
     *
     * @return HelloWorld|NULL
     *
     * @throws
     */
    public static function findById(\Database\IDatabaseConnection $connection, string $id) : HelloWorld {

        $sql = 'SELECT `id`, `name`
                FROM HelloWorld
                WHERE `id` = ?';

        $record = $connection->querySingle($sql, [$id]);
        if ($record == NULL)
            return NULL;

        $instance = new HelloWorld();

        $instance->id = strval($record['id']);
        $instance->name = strval($record['name']);

        return $instance;

    }

    /**
     * Convert an array into an instance of HelloWorld.
     *
     * @param array $input
     *
     * @return HelloWorld
     */
    public static function fromArray(array $input): HelloWorld {
        $instance = new HelloWorld();

        $instance->id = strval($input['id']);
        $instance->name = strval($input['name']);

        return $instance;
    }

    /**
     * Convert an instance of HelloWorld into an array.
     *
     * @return array
     */
    public function asArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    /**
     * Add
     *
     * @param \Database\IDatabaseConnection $connection
     *
     * @return bool
     * @throws
     */
    public function add(\Database\IDatabaseConnection $connection): bool {
        $sql = "INSERT INTO HelloWorld (`id`, `name`) VALUES (?, ?)";

        return $connection->execute($sql, [$this->id, $this->name]);
    }

    /**
     * Update
     *
     * @param \Database\IDatabaseConnection $connection
     *
     * @return bool
     * @throws
     */
    public function update(\Database\IDatabaseConnection $connection): bool {
        $sql = "UPDATE HelloWorld SET `id` = ?, `name` = ?
                WHERE `id` = ?";

        return $connection->execute($sql, [$this->id, $this->name, $this->id]);
    }

    /**
     * Delete
     *
     * @param \Database\IDatabaseConnection $connection
     *
     * @return bool
     * @throws
     */
    public function delete(\Database\IDatabaseConnection $connection): bool {
        $sql = "DELETE FROM HelloWorld WHERE `id` = ?";

        return $connection->execute($sql, [$this->id]);
    }
}
<?php

namespace AntonioKadid\MySql;

use Exception;
use Throwable;

/**
 * Class DatabaseException
 *
 * @package AntonioKadid\MySql
 */
class DatabaseException extends Exception
{
    /** @var array */
    private $_parameters;
    /** @var string */
    private $_query;

    /**
     * DatabaseException constructor.
     *
     * @param string         $message
     * @param string         $sqlQuery
     * @param array          $parameters
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', string $sqlQuery = '', array $parameters = [], int $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

        $this->_query = $sqlQuery;
        $this->_parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->_query;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->_parameters;
    }
}
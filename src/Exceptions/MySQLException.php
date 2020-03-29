<?php

namespace AntonioKadid\WAPPKitCore\Database\MySQL\Exceptions;

use Exception;
use Throwable;

/**
 * Class MySQLException
 *
 * @package AntonioKadid\WAPPKitCore\Database\MySQL\Exceptions
 */
class MySQLException extends Exception
{
    /** @var array */
    private $_parameters;
    /** @var string */
    private $_query;

    /**
     * MySQLException constructor.
     *
     * @param string         $message
     * @param string         $sqlQuery
     * @param array          $sqlQueryParameters
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', string $sqlQuery = '', array $sqlQueryParameters = [], int $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

        $this->_query = $sqlQuery;
        $this->_parameters = $sqlQueryParameters;
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
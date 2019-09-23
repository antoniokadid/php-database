<?php

namespace AntonioKadid\MySql;

use Exception;
use Throwable;

/**
 * Class DatabaseException
 *
 * @package AntonioKadid\MySql
 */
class DatabaseConnectionException extends Exception
{
    /**
     * DatabaseConnectionException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|NULL $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
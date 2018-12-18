<?php

namespace Database;

/**
 * Class DatabaseException
 *
 * @package Database
 */
class DatabaseException extends \Exception
{
    /**
     * DatabaseException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}
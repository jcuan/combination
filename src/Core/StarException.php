<?php
namespace Star\Core;

class StarException extends \Exception
{
    /**
     * StarException constructor.
     *
     * @param string $errorMsg
     * @param int $code
     * @param \Throwable|NULL $previous
     */
    public function __construct($errorMsg, $code = 0, \Throwable $previous = NULL)
    {
        parent::__construct($errorMsg, $code, $previous);
    }
}
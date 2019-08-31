<?php


namespace ProductBundle\Exceptions;


use Throwable;

class NotEnoughProductException extends \Exception
{
    public function __construct($message = "Not enough products!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
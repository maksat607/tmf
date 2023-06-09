<?php

namespace App\Exceptions;

use Exception;

class ValidationFieldException extends Exception
{
    public function __construct($message = 'You cannot vote for yourself!', $code = 422)
    {
        parent::__construct($message, $code);
    }
}

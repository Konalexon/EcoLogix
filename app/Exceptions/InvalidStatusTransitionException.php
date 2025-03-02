<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(string $message = "Invalid status transition", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}

// update 152 

// update 166 

// update 193 

// update 364 

// u206

// u251

// u254

// u275

// u328

// u335

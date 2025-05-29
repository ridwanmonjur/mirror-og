<?php

namespace App\Exceptions;

use Exception;

class BankAccountNeededException extends Exception
{
    public function __construct($message = "Bank account required for withdrawal")
    {
        parent::__construct($message);
    }
}

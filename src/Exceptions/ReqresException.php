<?php

namespace Cwp\Users\Exceptions;

use Exception;

class ReqresException extends Exception
{
    public function __toString()
    {
        return "Reqres API Failed: [{$this->code}]: {$this->message}\n";
    }
}

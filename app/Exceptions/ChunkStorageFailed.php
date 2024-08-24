<?php

namespace App\Exceptions;

use Exception;

class ChunkStorageFailed extends Exception
{
    public function __construct(
        $message = 'Unable to store chunk.',
        $code = 500,
        Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}

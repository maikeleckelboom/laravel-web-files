<?php

namespace App\Exceptions;

use Exception;

class ChunkCountMismatch extends Exception
{
    public function __construct(
        $message =  'The number of chunks is invalid. Please try again.',
        $code = 500,
        Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}

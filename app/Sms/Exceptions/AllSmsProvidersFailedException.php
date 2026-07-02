<?php

namespace App\Sms\Exceptions;

use RuntimeException;
use Throwable;

class AllSmsProvidersFailedException extends RuntimeException
{
    public function __construct(
        public string $to,
        public int $attempts,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "All SMS providers failed for {$to} after {$attempts} attempt(s).",
            0,
            $previous
        );
    }
}

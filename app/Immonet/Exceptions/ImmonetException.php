<?php

namespace App\Immonet\Exceptions;

use Exception;
use Throwable;

class ImmonetException extends Exception
{
    /**
     * @param  array<string, mixed>|null  $response
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public ?array $response = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the API response body.
     *
     * @return array<string, mixed>|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}

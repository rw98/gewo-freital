<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ImmoweltCredentialsData extends Data
{
    public function __construct(
        public string $ftpHost,
        public int $ftpPort = 21,
        public string $ftpUsername = '',
        public string $ftpPassword = '',
        public string $ftpPath = '/',
        public bool $ftpSsl = false,
        public string $providerId = '',
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->ftpHost)
            && ! empty($this->ftpUsername)
            && ! empty($this->ftpPassword);
    }
}

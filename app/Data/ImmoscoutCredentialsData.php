<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ImmoscoutCredentialsData extends Data
{
    public function __construct(
        public string $baseUrl,
        public string $consumerKey,
        public string $consumerSecret,
        public string $accessToken,
        public string $accessTokenSecret,
    ) {}

    public function isConfigured(): bool
    {
        return ! empty($this->consumerKey)
            && ! empty($this->consumerSecret)
            && ! empty($this->accessToken)
            && ! empty($this->accessTokenSecret);
    }
}

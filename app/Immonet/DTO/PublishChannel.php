<?php

namespace App\Immonet\DTO;

class PublishChannel
{
    /**
     * ImmobilienScout24 publish channel ID.
     */
    public const CHANNEL_IMMOSCOUT24 = 10000;

    public function __construct(
        public string $realEstateId,
        public int $channelId = self::CHANNEL_IMMOSCOUT24,
    ) {}

    /**
     * Convert to array for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'realEstate' => [
                '@id' => $this->realEstateId,
            ],
            'publishChannel' => [
                '@id' => $this->channelId,
            ],
        ];
    }
}

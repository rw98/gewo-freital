<?php

namespace App\Immonet;

use App\Immonet\Exceptions\ImmonetException;
use App\Immonet\Immowelt\ListingTransferService as ImmoweltService;
use App\Models\Listing;
use App\Models\User;

class TransferManager
{
    public const PLATFORM_IMMOSCOUT = 'immoscout';

    public const PLATFORM_IMMOWELT = 'immowelt';

    public function __construct(
        protected ListingTransferService $immoscoutService,
        protected ImmoweltService $immoweltService,
    ) {}

    /**
     * Transfer a listing to a specific platform.
     *
     * @return array<string, mixed>
     *
     * @throws ImmonetException
     */
    public function transfer(
        Listing $listing,
        string $platform,
        ?User $contact = null,
        bool $publish = true,
    ): array {
        return match ($platform) {
            self::PLATFORM_IMMOSCOUT => $this->immoscoutService->transfer($listing, $contact, $publish),
            self::PLATFORM_IMMOWELT => $this->immoweltService->transfer($listing, $contact),
            default => throw new ImmonetException("Unknown platform: {$platform}"),
        };
    }

    /**
     * Transfer a listing to multiple platforms.
     *
     * @param  array<string>  $platforms
     * @return array<string, array<string, mixed>>
     */
    public function transferToAll(
        Listing $listing,
        array $platforms,
        ?User $contact = null,
        bool $publish = true,
    ): array {
        $results = [];

        foreach ($platforms as $platform) {
            try {
                $results[$platform] = [
                    'success' => true,
                    'data' => $this->transfer($listing, $platform, $contact, $publish),
                ];
            } catch (\Exception $e) {
                $results[$platform] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Delete a listing from a specific platform.
     *
     * @throws ImmonetException
     */
    public function delete(Listing $listing, string $platform, ?string $externalId = null): void
    {
        match ($platform) {
            self::PLATFORM_IMMOSCOUT => $this->immoscoutService->delete($externalId ?? $listing->id),
            self::PLATFORM_IMMOWELT => $this->immoweltService->delete($listing),
            default => throw new ImmonetException("Unknown platform: {$platform}"),
        };
    }

    /**
     * Get available platforms.
     *
     * @return array<string, bool>
     */
    public function getAvailablePlatforms(): array
    {
        return [
            self::PLATFORM_IMMOSCOUT => $this->immoscoutService->isConfigured(),
            self::PLATFORM_IMMOWELT => $this->immoweltService->isConfigured(),
        ];
    }

    /**
     * Get configured platforms.
     *
     * @return array<string>
     */
    public function getConfiguredPlatforms(): array
    {
        return array_keys(array_filter($this->getAvailablePlatforms()));
    }

    /**
     * Check if a platform is configured.
     */
    public function isPlatformConfigured(string $platform): bool
    {
        return $this->getAvailablePlatforms()[$platform] ?? false;
    }

    /**
     * Get the ImmobilienScout24 service.
     */
    public function immoscout(): ListingTransferService
    {
        return $this->immoscoutService;
    }

    /**
     * Get the Immowelt service.
     */
    public function immowelt(): ImmoweltService
    {
        return $this->immoweltService;
    }
}

<?php

namespace App\Immonet;

use App\Immonet\DTO\Attachment;
use App\Immonet\DTO\Contact;
use App\Immonet\DTO\PublishChannel;
use App\Immonet\DTO\RealEstate;
use App\Immonet\Exceptions\ImmonetException;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ListingTransferService
{
    public function __construct(
        protected Client $client
    ) {}

    /**
     * Transfer a listing to ImmobilienScout24.
     *
     * @return array{real_estate_id: string, publish_id: string|null, attachment_ids: array<string>}
     *
     * @throws ImmonetException
     */
    public function transfer(Listing $listing, ?User $contactUser = null, bool $publish = true, bool $uploadImages = true): array
    {
        $result = [
            'real_estate_id' => '',
            'publish_id' => null,
            'attachment_ids' => [],
        ];

        // Create or get contact
        $contactId = null;
        if ($contactUser !== null) {
            $contact = Contact::fromUser($contactUser);
            $contactId = $this->client->createContact($contact);
            Log::info('ImmoScout24: Contact created', ['contact_id' => $contactId, 'user_id' => $contactUser->id]);
        }

        // Create real estate listing
        $realEstate = RealEstate::fromListing($listing, $contactId);
        $realEstateId = $this->client->createRealEstate($realEstate);
        $result['real_estate_id'] = $realEstateId;
        Log::info('ImmoScout24: Real estate created', ['real_estate_id' => $realEstateId, 'listing_id' => $listing->id]);

        // Upload images
        if ($uploadImages) {
            $result['attachment_ids'] = $this->uploadListingImages($listing, $realEstateId);
        }

        // Publish the listing
        if ($publish) {
            $publishChannel = new PublishChannel($realEstateId);
            $publishId = $this->client->publish($publishChannel);
            $result['publish_id'] = $publishId;
            Log::info('ImmoScout24: Listing published', ['publish_id' => $publishId, 'real_estate_id' => $realEstateId]);
        }

        return $result;
    }

    /**
     * Update an existing listing on ImmobilienScout24.
     *
     * @throws ImmonetException
     */
    public function update(Listing $listing, string $realEstateId, ?string $contactId = null): void
    {
        $realEstate = RealEstate::fromListing($listing, $contactId);
        $this->client->updateRealEstate($realEstateId, $realEstate);
        Log::info('ImmoScout24: Real estate updated', ['real_estate_id' => $realEstateId, 'listing_id' => $listing->id]);
    }

    /**
     * Delete a listing from ImmobilienScout24.
     *
     * @throws ImmonetException
     */
    public function delete(string $realEstateId, bool $unpublishFirst = true): void
    {
        if ($unpublishFirst) {
            try {
                $this->client->unpublish($realEstateId);
                Log::info('ImmoScout24: Listing unpublished', ['real_estate_id' => $realEstateId]);
            } catch (ImmonetException $e) {
                // Ignore if not published
                Log::warning('ImmoScout24: Could not unpublish listing', [
                    'real_estate_id' => $realEstateId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->client->deleteRealEstate($realEstateId);
        Log::info('ImmoScout24: Real estate deleted', ['real_estate_id' => $realEstateId]);
    }

    /**
     * Upload all images for a listing.
     *
     * @return array<string>
     *
     * @throws ImmonetException
     */
    public function uploadListingImages(Listing $listing, string $realEstateId): array
    {
        $attachmentIds = [];
        $images = $listing->images;

        foreach ($images as $index => $image) {
            try {
                $attachment = Attachment::fromImage($image, isTitlePicture: $index === 0);
                $attachmentId = $this->client->uploadAttachment($realEstateId, $attachment);
                $attachmentIds[] = $attachmentId;
                Log::info('ImmoScout24: Attachment uploaded', [
                    'attachment_id' => $attachmentId,
                    'real_estate_id' => $realEstateId,
                    'image_id' => $image->id,
                ]);
            } catch (\Exception $e) {
                Log::error('ImmoScout24: Failed to upload attachment', [
                    'real_estate_id' => $realEstateId,
                    'image_id' => $image->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $attachmentIds;
    }

    /**
     * Check if the client is configured.
     */
    public function isConfigured(): bool
    {
        return $this->client->isConfigured();
    }

    /**
     * Get the underlying client.
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}

<?php

namespace App\Immonet;

use App\Immonet\DTO\Attachment;
use App\Immonet\DTO\Contact;
use App\Immonet\DTO\PublishChannel;
use App\Immonet\DTO\RealEstate;
use App\Immonet\Exceptions\ImmonetException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    protected string $baseUrl;

    protected string $consumerKey;

    protected string $consumerSecret;

    protected string $accessToken;

    protected string $accessTokenSecret;

    public function __construct(
        ?string $baseUrl = null,
        ?string $consumerKey = null,
        ?string $consumerSecret = null,
        ?string $accessToken = null,
        ?string $accessTokenSecret = null,
    ) {
        $this->baseUrl = $baseUrl ?? config('services.immoscout.base_url', 'https://rest.immobilienscout24.de/restapi/api');
        $this->consumerKey = $consumerKey ?? config('services.immoscout.consumer_key', '');
        $this->consumerSecret = $consumerSecret ?? config('services.immoscout.consumer_secret', '');
        $this->accessToken = $accessToken ?? config('services.immoscout.access_token', '');
        $this->accessTokenSecret = $accessTokenSecret ?? config('services.immoscout.access_token_secret', '');
    }

    /**
     * Create a new real estate listing.
     *
     * @throws ImmonetException
     */
    public function createRealEstate(RealEstate $realEstate): string
    {
        $response = $this->request()
            ->post($this->buildUrl('/offer/v1.0/user/me/realestate'), [
                'realestates.apartmentRent' => $realEstate->toArray(),
            ]);

        $this->handleResponse($response);

        return $this->extractResourceId($response);
    }

    /**
     * Update an existing real estate listing.
     *
     * @throws ImmonetException
     */
    public function updateRealEstate(string $realEstateId, RealEstate $realEstate): void
    {
        $response = $this->request()
            ->put($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}"), [
                'realestates.apartmentRent' => $realEstate->toArray(),
            ]);

        $this->handleResponse($response);
    }

    /**
     * Delete a real estate listing.
     *
     * @throws ImmonetException
     */
    public function deleteRealEstate(string $realEstateId): void
    {
        $response = $this->request()
            ->delete($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}"));

        $this->handleResponse($response);
    }

    /**
     * Get a real estate listing by ID.
     *
     * @return array<string, mixed>
     *
     * @throws ImmonetException
     */
    public function getRealEstate(string $realEstateId): array
    {
        $response = $this->request()
            ->get($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}"));

        $this->handleResponse($response);

        return $response->json();
    }

    /**
     * Get all real estate listings.
     *
     * @return array<string, mixed>
     *
     * @throws ImmonetException
     */
    public function getAllRealEstates(int $pageNumber = 1, int $pageSize = 20): array
    {
        $response = $this->request()
            ->get($this->buildUrl('/offer/v1.0/user/me/realestate'), [
                'pagenumber' => $pageNumber,
                'pagesize' => $pageSize,
            ]);

        $this->handleResponse($response);

        return $response->json();
    }

    /**
     * Publish a real estate listing to a channel.
     *
     * @throws ImmonetException
     */
    public function publish(PublishChannel $publishChannel): string
    {
        $response = $this->request()
            ->post($this->buildUrl('/offer/v1.0/publish'), [
                'publishObjects' => [
                    'publishObject' => $publishChannel->toArray(),
                ],
            ]);

        $this->handleResponse($response);

        return $this->extractResourceId($response);
    }

    /**
     * Unpublish a real estate listing from a channel.
     *
     * @throws ImmonetException
     */
    public function unpublish(string $realEstateId, int $channelId = 10000): void
    {
        $response = $this->request()
            ->delete($this->buildUrl("/offer/v1.0/publish/{$realEstateId}_{$channelId}"));

        $this->handleResponse($response);
    }

    /**
     * Create a contact.
     *
     * @throws ImmonetException
     */
    public function createContact(Contact $contact): string
    {
        $response = $this->request()
            ->post($this->buildUrl('/offer/v1.0/user/me/contact'), [
                'common.realtorContactDetails' => $contact->toArray(),
            ]);

        $this->handleResponse($response);

        return $this->extractResourceId($response);
    }

    /**
     * Get a contact by ID.
     *
     * @return array<string, mixed>
     *
     * @throws ImmonetException
     */
    public function getContact(string $contactId): array
    {
        $response = $this->request()
            ->get($this->buildUrl("/offer/v1.0/user/me/contact/{$contactId}"));

        $this->handleResponse($response);

        return $response->json();
    }

    /**
     * Upload an attachment to a real estate listing.
     *
     * @throws ImmonetException
     */
    public function uploadAttachment(string $realEstateId, Attachment $attachment): string
    {
        $response = $this->request()
            ->attach('attachment', $attachment->content, $attachment->filename, [
                'Content-Type' => $attachment->mimeType,
            ])
            ->post($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}/attachment"), [
                'title' => $attachment->title,
                'floorplan' => $attachment->isFloorplan,
                'titlePicture' => $attachment->isTitlePicture,
            ]);

        $this->handleResponse($response);

        return $this->extractResourceId($response);
    }

    /**
     * Delete an attachment from a real estate listing.
     *
     * @throws ImmonetException
     */
    public function deleteAttachment(string $realEstateId, string $attachmentId): void
    {
        $response = $this->request()
            ->delete($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}/attachment/{$attachmentId}"));

        $this->handleResponse($response);
    }

    /**
     * Get all attachments for a real estate listing.
     *
     * @return array<string, mixed>
     *
     * @throws ImmonetException
     */
    public function getAttachments(string $realEstateId): array
    {
        $response = $this->request()
            ->get($this->buildUrl("/offer/v1.0/user/me/realestate/{$realEstateId}/attachment"));

        $this->handleResponse($response);

        return $response->json();
    }

    /**
     * Build the full URL for an API endpoint.
     */
    protected function buildUrl(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
    }

    /**
     * Create a configured HTTP request.
     */
    protected function request(): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withToken($this->generateOAuthHeader(), 'OAuth');
    }

    /**
     * Generate OAuth 1.0 authorization header.
     */
    protected function generateOAuthHeader(): string
    {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_token' => $this->accessToken,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => bin2hex(random_bytes(16)),
            'oauth_version' => '1.0',
        ];

        // Build signature base string
        $signatureKey = rawurlencode($this->consumerSecret).'&'.rawurlencode($this->accessTokenSecret);
        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $this->buildSignatureBaseString($oauth), $signatureKey, true));

        // Build header string
        $headerParts = [];
        foreach ($oauth as $key => $value) {
            $headerParts[] = rawurlencode($key).'="'.rawurlencode($value).'"';
        }

        return implode(', ', $headerParts);
    }

    /**
     * Build the OAuth signature base string.
     *
     * @param  array<string, mixed>  $oauth
     */
    protected function buildSignatureBaseString(array $oauth): string
    {
        ksort($oauth);
        $params = [];
        foreach ($oauth as $key => $value) {
            $params[] = rawurlencode($key).'='.rawurlencode($value);
        }

        return 'POST&'.rawurlencode($this->baseUrl).'&'.rawurlencode(implode('&', $params));
    }

    /**
     * Handle API response and throw exception on error.
     *
     * @throws ImmonetException
     */
    protected function handleResponse(Response $response): void
    {
        if ($response->failed()) {
            $body = $response->json();
            $message = $body['message'] ?? $body['common.messages'][0]['message'] ?? 'Unknown API error';

            throw new ImmonetException(
                message: $message,
                code: $response->status(),
                response: $body
            );
        }
    }

    /**
     * Extract resource ID from API response.
     */
    protected function extractResourceId(Response $response): string
    {
        $body = $response->json();

        // Try different response formats
        if (isset($body['common.messages'][0]['id'])) {
            return (string) $body['common.messages'][0]['id'];
        }

        if (isset($body['id'])) {
            return (string) $body['id'];
        }

        // Extract from message like "Resource [realestate] with id [315652241] has been created."
        if (isset($body['common.messages'][0]['message'])) {
            if (preg_match('/id \[(\d+)\]/', $body['common.messages'][0]['message'], $matches)) {
                return $matches[1];
            }
        }

        throw new ImmonetException('Could not extract resource ID from response', response: $body);
    }

    /**
     * Set the base URL.
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get the current base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Check if the client is configured with credentials.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->consumerKey)
            && ! empty($this->consumerSecret)
            && ! empty($this->accessToken)
            && ! empty($this->accessTokenSecret);
    }
}

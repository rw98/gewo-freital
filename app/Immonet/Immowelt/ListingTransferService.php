<?php

namespace App\Immonet\Immowelt;

use App\Immonet\Exceptions\ImmonetException;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ListingTransferService
{
    public function __construct(
        protected Client $client,
        protected OpenImmoGenerator $generator,
    ) {}

    /**
     * Transfer a listing to Immowelt via OpenImmo FTP.
     *
     * @return array{filename: string, xml: string}
     *
     * @throws ImmonetException
     */
    public function transfer(Listing $listing, ?User $contact = null): array
    {
        // Generate OpenImmo XML
        $xml = $this->generator->generate($listing, $contact);

        // Collect image paths
        $imagePaths = $listing->images->pluck('path')->toArray();

        // Generate unique filename
        $filename = 'listing_'.$listing->id.'_'.time().'.zip';

        // Upload to FTP
        $this->client->uploadOpenImmo($xml, $imagePaths, $filename);

        Log::info('Immowelt: Listing transferred', [
            'listing_id' => $listing->id,
            'filename' => $filename,
        ]);

        return [
            'filename' => $filename,
            'xml' => $xml,
        ];
    }

    /**
     * Update an existing listing on Immowelt.
     *
     * @return array{filename: string, xml: string}
     *
     * @throws ImmonetException
     */
    public function update(Listing $listing, ?User $contact = null): array
    {
        // For OpenImmo, update is the same as transfer (CHANGE action)
        return $this->transfer($listing, $contact);
    }

    /**
     * Delete a listing from Immowelt.
     *
     * @return array{filename: string, xml: string}
     *
     * @throws ImmonetException
     */
    public function delete(Listing $listing): array
    {
        $providerId = config('services.immowelt.provider_id', 'GEWO');

        // Generate delete XML
        $xml = $this->generator->generateDelete($listing->id, $providerId);

        // Generate unique filename
        $filename = 'delete_'.$listing->id.'_'.time().'.zip';

        // Upload to FTP
        $this->client->uploadOpenImmo($xml, [], $filename);

        Log::info('Immowelt: Listing deletion requested', [
            'listing_id' => $listing->id,
            'filename' => $filename,
        ]);

        return [
            'filename' => $filename,
            'xml' => $xml,
        ];
    }

    /**
     * Transfer multiple listings in a single ZIP file.
     *
     * @param  \Illuminate\Support\Collection<int, Listing>|array<Listing>  $listings
     * @return array{filename: string, xml: string}
     *
     * @throws ImmonetException
     */
    public function transferBatch(iterable $listings, ?User $contact = null): array
    {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElement('openimmo');
        $root->setAttribute('xmlns', 'http://www.openimmo.de');
        $doc->appendChild($root);

        // Add uebertragung
        $uebertragung = $doc->createElement('uebertragung');
        $uebertragung->setAttribute('art', 'CHANGE');
        $uebertragung->setAttribute('modus', 'CHANGE');
        $uebertragung->setAttribute('version', '1.2.7');
        $uebertragung->setAttribute('sendersoftware', 'GEWO');
        $uebertragung->setAttribute('senderversion', '1.0');
        $uebertragung->setAttribute('tecession', date('Y-m-d\TH:i:s'));
        $root->appendChild($uebertragung);

        // Add anbieter with all listings
        $anbieter = $doc->createElement('anbieter');
        $anbieternr = $doc->createElement('anbieternr');
        $anbieternr->nodeValue = config('services.immowelt.provider_id', 'GEWO');
        $anbieter->appendChild($anbieternr);

        $firma = $doc->createElement('firma');
        $firma->nodeValue = config('app.name', 'GEWO');
        $anbieter->appendChild($firma);

        $allImagePaths = [];

        foreach ($listings as $listing) {
            // Generate individual listing XML and import it
            $listingXml = $this->generator->generate($listing, $contact);
            $listingDoc = new \DOMDocument;
            $listingDoc->loadXML($listingXml);

            // Find the immobilie element and import it
            $immobilieElements = $listingDoc->getElementsByTagName('immobilie');
            if ($immobilieElements->length > 0) {
                $importedNode = $doc->importNode($immobilieElements->item(0), true);
                $anbieter->appendChild($importedNode);
            }

            // Collect image paths
            foreach ($listing->images as $image) {
                $allImagePaths[] = $image->path;
            }
        }

        $root->appendChild($anbieter);

        $xml = $doc->saveXML();
        $filename = 'batch_'.time().'.zip';

        // Upload to FTP
        $this->client->uploadOpenImmo($xml, $allImagePaths, $filename);

        Log::info('Immowelt: Batch transfer completed', [
            'count' => count($listings instanceof \Countable ? $listings : iterator_to_array($listings)),
            'filename' => $filename,
        ]);

        return [
            'filename' => $filename,
            'xml' => $xml,
        ];
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

    /**
     * Get the OpenImmo generator.
     */
    public function getGenerator(): OpenImmoGenerator
    {
        return $this->generator;
    }
}

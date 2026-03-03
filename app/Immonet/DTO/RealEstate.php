<?php

namespace App\Immonet\DTO;

use App\Models\Listing;

class RealEstate
{
    public function __construct(
        public string $title,
        public string $street,
        public string $houseNumber,
        public string $postcode,
        public string $city,
        public bool $showAddress = true,
        public float $baseRent = 0,
        public float $serviceCharge = 0,
        public string $currency = 'EUR',
        public float $livingSpace = 0,
        public float $numberOfRooms = 0,
        public ?int $floor = null,
        public ?int $numberOfFloors = null,
        public ?int $yearConstructed = null,
        public ?string $description = null,
        public ?string $freeFrom = null,
        public bool $balcony = false,
        public bool $terrace = false,
        public bool $garden = false,
        public bool $lift = false,
        public bool $barrierFree = false,
        public ?string $petsAllowed = null,
        public ?string $courtage = null,
        public bool $courtageHasValue = false,
        public ?string $externalId = null,
        public ?string $contactId = null,
    ) {}

    /**
     * Create a RealEstate DTO from a Listing model.
     */
    public static function fromListing(Listing $listing, ?string $contactId = null): self
    {
        return new self(
            title: $listing->title,
            street: $listing->street,
            houseNumber: $listing->street_number,
            postcode: $listing->postal_code,
            city: $listing->city,
            showAddress: true,
            baseRent: (float) $listing->rent_cold,
            serviceCharge: (float) $listing->utility_cost,
            livingSpace: (float) $listing->size_sqm,
            numberOfRooms: (float) $listing->rooms,
            floor: $listing->floor,
            yearConstructed: $listing->year_built,
            description: $listing->description,
            freeFrom: $listing->available_from?->format('Y-m-d'),
            balcony: (bool) $listing->has_balcony,
            terrace: (bool) $listing->has_terrace,
            lift: (bool) $listing->has_elevator,
            barrierFree: (bool) $listing->is_wheelchair_accessible,
            petsAllowed: $listing->pets_allowed ? 'YES' : 'NO',
            externalId: $listing->id,
            contactId: $contactId,
        );
    }

    /**
     * Convert to array for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'title' => mb_substr($this->title, 0, 100),
            'address' => [
                'street' => $this->street,
                'houseNumber' => $this->houseNumber,
                'postcode' => $this->postcode,
                'city' => $this->city,
            ],
            'showAddress' => $this->showAddress,
            'baseRent' => $this->baseRent,
            'serviceCharge' => $this->serviceCharge,
            'currency' => $this->currency,
            'livingSpace' => $this->livingSpace,
            'numberOfRooms' => $this->numberOfRooms,
        ];

        if ($this->floor !== null) {
            $data['floor'] = $this->floor;
        }

        if ($this->numberOfFloors !== null) {
            $data['numberOfFloors'] = $this->numberOfFloors;
        }

        if ($this->yearConstructed !== null) {
            $data['yearConstructed'] = $this->yearConstructed;
        }

        if ($this->description !== null) {
            $data['descriptionNote'] = $this->description;
        }

        if ($this->freeFrom !== null) {
            $data['freeFrom'] = $this->freeFrom;
        }

        $data['balcony'] = $this->balcony;
        $data['terrace'] = $this->terrace;
        $data['garden'] = $this->garden;
        $data['lift'] = $this->lift;
        $data['barrierFree'] = $this->barrierFree;

        if ($this->petsAllowed !== null) {
            $data['petsAllowed'] = $this->petsAllowed;
        }

        if ($this->courtage !== null) {
            $data['courtage'] = [
                'hasCourtage' => $this->courtageHasValue,
                'courtage' => $this->courtage,
            ];
        }

        if ($this->externalId !== null) {
            $data['externalId'] = $this->externalId;
        }

        if ($this->contactId !== null) {
            $data['contact'] = [
                '@id' => $this->contactId,
            ];
        }

        return $data;
    }
}

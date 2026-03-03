<?php

namespace App\Immonet\DTO;

use App\Models\User;

class Contact
{
    public function __construct(
        public string $email,
        public ?string $salutation = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $phone = null,
        public ?string $fax = null,
        public ?string $mobile = null,
        public ?string $company = null,
        public ?string $street = null,
        public ?string $houseNumber = null,
        public ?string $postcode = null,
        public ?string $city = null,
        public string $countryCode = 'DEU',
        public ?string $homepageUrl = null,
        public ?string $externalId = null,
    ) {}

    /**
     * Create a Contact DTO from a User model.
     */
    public static function fromUser(User $user): self
    {
        return new self(
            email: $user->email,
            firstname: $user->first_name,
            lastname: $user->last_name,
            externalId: $user->id,
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
            'email' => $this->email,
            'countryCode' => $this->countryCode,
        ];

        if ($this->salutation !== null) {
            $data['salutation'] = $this->salutation;
        }

        if ($this->firstname !== null) {
            $data['firstname'] = $this->firstname;
        }

        if ($this->lastname !== null) {
            $data['lastname'] = $this->lastname;
        }

        if ($this->phone !== null) {
            $data['phoneNumber'] = $this->phone;
        }

        if ($this->fax !== null) {
            $data['faxNumber'] = $this->fax;
        }

        if ($this->mobile !== null) {
            $data['cellPhoneNumber'] = $this->mobile;
        }

        if ($this->company !== null) {
            $data['company'] = $this->company;
        }

        if ($this->street !== null || $this->houseNumber !== null || $this->postcode !== null || $this->city !== null) {
            $data['address'] = [];

            if ($this->street !== null) {
                $data['address']['street'] = $this->street;
            }

            if ($this->houseNumber !== null) {
                $data['address']['houseNumber'] = $this->houseNumber;
            }

            if ($this->postcode !== null) {
                $data['address']['postcode'] = $this->postcode;
            }

            if ($this->city !== null) {
                $data['address']['city'] = $this->city;
            }
        }

        if ($this->homepageUrl !== null) {
            $data['homepageUrl'] = $this->homepageUrl;
        }

        if ($this->externalId !== null) {
            $data['externalId'] = $this->externalId;
        }

        return $data;
    }
}

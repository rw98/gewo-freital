<?php

namespace Database\Factories;

use App\Data\ImmoscoutCredentialsData;
use App\Data\ImmoweltCredentialsData;
use App\Enums\IntegrationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingDistribution>
 */
class ListingDistributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(IntegrationType::cases());

        return [
            'type' => $type,
            'name' => $type->label(),
            'credentials' => $this->credentialsForType($type),
            'is_enabled' => true,
        ];
    }

    /**
     * Generate credentials for a specific integration type.
     */
    private function credentialsForType(IntegrationType $type): ImmoscoutCredentialsData|ImmoweltCredentialsData
    {
        return match ($type) {
            IntegrationType::Immoscout => new ImmoscoutCredentialsData(
                baseUrl: 'https://rest.sandbox-immobilienscout24.de/restapi/api',
                consumerKey: fake()->uuid(),
                consumerSecret: fake()->sha256(),
                accessToken: fake()->uuid(),
                accessTokenSecret: fake()->sha256(),
            ),
            IntegrationType::Immowelt => new ImmoweltCredentialsData(
                ftpHost: 'ftp.immowelt.de',
                ftpPort: 21,
                ftpUsername: fake()->userName(),
                ftpPassword: fake()->password(),
                ftpPath: '/',
                ftpSsl: true,
                providerId: fake()->numerify('GEWO#####'),
            ),
        };
    }

    /**
     * Set the distribution to use Immoscout.
     */
    public function immoscout(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => IntegrationType::Immoscout,
            'name' => 'ImmobilienScout24',
            'credentials' => new ImmoscoutCredentialsData(
                baseUrl: 'https://rest.sandbox-immobilienscout24.de/restapi/api',
                consumerKey: fake()->uuid(),
                consumerSecret: fake()->sha256(),
                accessToken: fake()->uuid(),
                accessTokenSecret: fake()->sha256(),
            ),
        ]);
    }

    /**
     * Set the distribution to use Immowelt.
     */
    public function immowelt(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => IntegrationType::Immowelt,
            'name' => 'Immowelt',
            'credentials' => new ImmoweltCredentialsData(
                ftpHost: 'ftp.immowelt.de',
                ftpPort: 21,
                ftpUsername: fake()->userName(),
                ftpPassword: fake()->password(),
                ftpPath: '/',
                ftpSsl: true,
                providerId: fake()->numerify('GEWO#####'),
            ),
        ]);
    }

    /**
     * Set the distribution as disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}

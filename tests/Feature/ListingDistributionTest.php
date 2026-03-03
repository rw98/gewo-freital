<?php

use App\Data\ImmoscoutCredentialsData;
use App\Data\ImmoweltCredentialsData;
use App\Enums\IntegrationType;
use App\Models\Listing;
use App\Models\ListingDistribution;

it('can create an immoscout distribution', function () {
    $distribution = ListingDistribution::factory()->immoscout()->create();

    expect($distribution->type)->toBe(IntegrationType::Immoscout);
    expect($distribution->getTypedCredentials())->toBeInstanceOf(ImmoscoutCredentialsData::class);
    expect($distribution->isConfigured())->toBeTrue();
});

it('can create an immowelt distribution', function () {
    $distribution = ListingDistribution::factory()->immowelt()->create();

    expect($distribution->type)->toBe(IntegrationType::Immowelt);
    expect($distribution->getTypedCredentials())->toBeInstanceOf(ImmoweltCredentialsData::class);
    expect($distribution->isConfigured())->toBeTrue();
});

it('encrypts credentials in the database', function () {
    $distribution = ListingDistribution::factory()->immoscout()->create();

    // Raw database value should be encrypted (not JSON-readable)
    $rawCredentials = \DB::table('listing_distributions')
        ->where('id', $distribution->id)
        ->value('credentials');

    expect($rawCredentials)->not->toContain('consumerKey');
    expect($rawCredentials)->not->toContain('{');
});

it('can attach listings to a distribution channel', function () {
    $distribution = ListingDistribution::factory()->immoscout()->create();
    $listing = Listing::factory()->create();

    $distribution->listings()->attach($listing->id, [
        'external_id' => 'IS24-123456',
        'last_synced_at' => now(),
    ]);

    expect($distribution->listings)->toHaveCount(1);
    expect($distribution->listings->first()->pivot->external_id)->toBe('IS24-123456');
});

it('can access distributions from a listing', function () {
    $listing = Listing::factory()->create();
    $immoscout = ListingDistribution::factory()->immoscout()->create();
    $immowelt = ListingDistribution::factory()->immowelt()->create();

    $listing->distributions()->attach([
        $immoscout->id => ['external_id' => 'IS24-123'],
        $immowelt->id => ['external_id' => 'IW-456'],
    ]);

    expect($listing->distributions)->toHaveCount(2);
});

it('scopes enabled distributions', function () {
    ListingDistribution::factory()->immoscout()->create(['is_enabled' => true]);
    ListingDistribution::factory()->immowelt()->disabled()->create();

    expect(ListingDistribution::enabled()->count())->toBe(1);
});

it('scopes distributions by type', function () {
    ListingDistribution::factory()->immoscout()->create();
    ListingDistribution::factory()->immowelt()->create();

    expect(ListingDistribution::ofType(IntegrationType::Immoscout)->count())->toBe(1);
});

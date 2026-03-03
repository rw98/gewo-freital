<?php

namespace App\Models;

use App\Casts\EncryptedCredentialsCast;
use App\Data\ImmoscoutCredentialsData;
use App\Data\ImmoweltCredentialsData;
use App\Enums\IntegrationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\LaravelData\Data;

class ListingDistribution extends Model
{
    /** @use HasFactory<\Database\Factories\ListingDistributionFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'credentials',
        'name',
        'is_enabled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => IntegrationType::class,
            'credentials' => EncryptedCredentialsCast::class,
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * Get the listings distributed to this channel.
     *
     * @return BelongsToMany<Listing, $this>
     */
    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class)
            ->withPivot(['external_id', 'last_synced_at', 'sync_error'])
            ->withTimestamps();
    }

    /**
     * Get the typed credentials for this integration.
     *
     * @return ImmoscoutCredentialsData|ImmoweltCredentialsData|null
     */
    public function getTypedCredentials(): ?Data
    {
        return $this->credentials;
    }

    /**
     * Check if the credentials are fully configured.
     */
    public function isConfigured(): bool
    {
        $credentials = $this->getTypedCredentials();

        if ($credentials === null) {
            return false;
        }

        return $credentials->isConfigured();
    }

    /**
     * Scope to get only enabled distributions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get distributions by type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeOfType($query, IntegrationType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get configured distributions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeConfigured($query)
    {
        return $query->enabled()->whereNotNull('credentials');
    }
}

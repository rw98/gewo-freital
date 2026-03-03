<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestTenant extends Model
{
    /** @use HasFactory<\Database\Factories\RequestTenantFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_request_id',
        'pays_rent',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'relationship',
        'employment_status',
        'monthly_net_income',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pays_rent' => 'boolean',
            'date_of_birth' => 'date',
            'employment_status' => EmploymentStatus::class,
            'monthly_net_income' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<ListingRequest, $this>
     */
    public function listingRequest(): BelongsTo
    {
        return $this->belongsTo(ListingRequest::class);
    }

    /**
     * Get the full name of the tenant.
     */
    public function fullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Calculate age from date of birth.
     */
    public function age(): ?int
    {
        return $this->date_of_birth?->age;
    }
}

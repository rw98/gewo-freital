<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormResponse extends Model
{
    /** @use HasFactory<\Database\Factories\FormResponseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_id',
        'listing_request_id',
        'submitter_email',
        'submitter_name',
        'ip_address',
        'submitted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * @return BelongsTo<ListingRequest, $this>
     */
    public function listingRequest(): BelongsTo
    {
        return $this->belongsTo(ListingRequest::class);
    }

    /**
     * @return HasMany<FormFieldValue, $this>
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(FormFieldValue::class);
    }

    /**
     * Get the value for a specific field.
     */
    public function getFieldValue(string $fieldId): ?FormFieldValue
    {
        return $this->fieldValues->firstWhere('form_field_id', $fieldId);
    }
}

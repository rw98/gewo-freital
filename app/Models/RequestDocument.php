<?php

namespace App\Models;

use App\Enums\RequestDocumentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestDocument extends Model
{
    /** @use HasFactory<\Database\Factories\RequestDocumentFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_request_id',
        'uploaded_by_user_id',
        'type',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'size_bytes',
        'uploaded_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => RequestDocumentType::class,
            'size_bytes' => 'integer',
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
     * @return BelongsTo<User, $this>
     */
    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Check if the document was uploaded by a requestee.
     */
    public function isUploadedByRequestee(): bool
    {
        return $this->uploaded_by === 'requestee';
    }

    /**
     * Check if the document was uploaded by an employee.
     */
    public function isUploadedByEmployee(): bool
    {
        return $this->uploaded_by === 'employee';
    }

    /**
     * Get the human-readable file size.
     */
    public function humanReadableSize(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2).' '.$units[$index];
    }
}

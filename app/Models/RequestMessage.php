<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestMessage extends Model
{
    /** @use HasFactory<\Database\Factories\RequestMessageFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'listing_request_id',
        'user_id',
        'sender_type',
        'content',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the message was sent by a requestee.
     */
    public function isSentByRequestee(): bool
    {
        return $this->sender_type === 'requestee';
    }

    /**
     * Check if the message was sent by an employee.
     */
    public function isSentByEmployee(): bool
    {
        return $this->sender_type === 'employee';
    }

    /**
     * Check if the message has been read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Scope to get only unread messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}

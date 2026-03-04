<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormFieldValue extends Model
{
    /** @use HasFactory<\Database\Factories\FormFieldValueFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_response_id',
        'form_field_id',
        'value',
        'file_path',
        'file_name',
    ];

    /**
     * @return BelongsTo<FormResponse, $this>
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(FormResponse::class, 'form_response_id');
    }

    /**
     * @return BelongsTo<FormField, $this>
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }

    /**
     * Get the file URL if this value has a file.
     */
    public function getFileUrl(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Check if this value has a file.
     */
    public function hasFile(): bool
    {
        return ! empty($this->file_path);
    }
}

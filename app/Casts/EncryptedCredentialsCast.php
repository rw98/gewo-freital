<?php

namespace App\Casts;

use App\Data\ImmoscoutCredentialsData;
use App\Data\ImmoweltCredentialsData;
use App\Enums\IntegrationType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Spatie\LaravelData\Data;

/**
 * @implements CastsAttributes<Data, Data>
 */
class EncryptedCredentialsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Data
    {
        if ($value === null) {
            return null;
        }

        $decrypted = Crypt::decryptString($value);
        $data = json_decode($decrypted, true);

        if ($data === null) {
            return null;
        }

        $type = isset($attributes['type'])
            ? IntegrationType::tryFrom($attributes['type'])
            : null;

        return match ($type) {
            IntegrationType::Immoscout => ImmoscoutCredentialsData::from($data),
            IntegrationType::Immowelt => ImmoweltCredentialsData::from($data),
            default => null,
        };
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Data) {
            $value = $value->toArray();
        }

        return Crypt::encryptString(json_encode($value));
    }
}

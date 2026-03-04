<?php

namespace App\Services;

use App\Models\Flat;
use App\Models\Listing;
use App\Models\ListingRequest;
use Illuminate\Database\Eloquent\Model;

class FormAutoFillService
{
    /**
     * Available source types for auto-fill.
     *
     * @return array<string, string>
     */
    public static function sourceTypes(): array
    {
        return [
            'listing' => 'Inserat',
            'listing_request' => 'Anfrage',
            'flat' => 'Wohnung',
        ];
    }

    /**
     * Get available fields for a source type.
     *
     * @return array<string, array{label: string, type: string}>
     */
    public static function fieldsForSource(string $sourceType): array
    {
        return match ($sourceType) {
            'listing' => self::listingFields(),
            'listing_request' => self::listingRequestFields(),
            'flat' => self::flatFields(),
            default => [],
        };
    }

    /**
     * @return array<string, array{label: string, type: string}>
     */
    private static function listingFields(): array
    {
        return [
            'title' => ['label' => 'Titel', 'type' => 'text'],
            'description' => ['label' => 'Beschreibung', 'type' => 'textarea'],
            'street' => ['label' => 'Straße', 'type' => 'text'],
            'street_number' => ['label' => 'Hausnummer', 'type' => 'text'],
            'postal_code' => ['label' => 'PLZ', 'type' => 'text'],
            'city' => ['label' => 'Stadt', 'type' => 'text'],
            'full_address' => ['label' => 'Vollständige Adresse', 'type' => 'text'],
            'size_sqm' => ['label' => 'Größe (m²)', 'type' => 'number'],
            'rent_cold' => ['label' => 'Kaltmiete', 'type' => 'number'],
            'utility_cost' => ['label' => 'Nebenkosten', 'type' => 'number'],
            'total_rent' => ['label' => 'Gesamtmiete', 'type' => 'number'],
            'rooms' => ['label' => 'Zimmer', 'type' => 'number'],
            'floor' => ['label' => 'Etage', 'type' => 'number'],
            'flat_number' => ['label' => 'Wohnungsnummer', 'type' => 'text'],
            'year_built' => ['label' => 'Baujahr', 'type' => 'number'],
            'available_from' => ['label' => 'Verfügbar ab', 'type' => 'date'],
            'has_balcony' => ['label' => 'Balkon vorhanden', 'type' => 'checkbox'],
            'has_terrace' => ['label' => 'Terrasse vorhanden', 'type' => 'checkbox'],
            'has_elevator' => ['label' => 'Aufzug vorhanden', 'type' => 'checkbox'],
            'is_wheelchair_accessible' => ['label' => 'Barrierefrei', 'type' => 'checkbox'],
            'pets_allowed' => ['label' => 'Haustiere erlaubt', 'type' => 'checkbox'],
        ];
    }

    /**
     * @return array<string, array{label: string, type: string}>
     */
    private static function listingRequestFields(): array
    {
        return [
            'first_name' => ['label' => 'Vorname', 'type' => 'text'],
            'middle_name' => ['label' => 'Zweiter Vorname', 'type' => 'text'],
            'last_name' => ['label' => 'Nachname', 'type' => 'text'],
            'full_name' => ['label' => 'Vollständiger Name', 'type' => 'text'],
            'email' => ['label' => 'E-Mail', 'type' => 'email'],
            'phone' => ['label' => 'Telefon', 'type' => 'phone'],
            'message' => ['label' => 'Nachricht', 'type' => 'textarea'],
            'has_pets' => ['label' => 'Haustiere', 'type' => 'checkbox'],
            'pets_details' => ['label' => 'Haustier-Details', 'type' => 'text'],
            'is_smoker' => ['label' => 'Raucher', 'type' => 'checkbox'],
            'current_landlord_name' => ['label' => 'Name aktueller Vermieter', 'type' => 'text'],
            'current_landlord_phone' => ['label' => 'Telefon aktueller Vermieter', 'type' => 'phone'],
            'current_landlord_email' => ['label' => 'E-Mail aktueller Vermieter', 'type' => 'email'],
            'reason_for_moving' => ['label' => 'Umzugsgrund', 'type' => 'textarea'],
            'desired_move_in_date' => ['label' => 'Gewünschter Einzugstermin', 'type' => 'date'],
            'additional_notes' => ['label' => 'Zusätzliche Anmerkungen', 'type' => 'textarea'],
            // Related listing fields
            'listing.title' => ['label' => 'Inserat: Titel', 'type' => 'text'],
            'listing.full_address' => ['label' => 'Inserat: Adresse', 'type' => 'text'],
            'listing.size_sqm' => ['label' => 'Inserat: Größe (m²)', 'type' => 'number'],
            'listing.total_rent' => ['label' => 'Inserat: Gesamtmiete', 'type' => 'number'],
        ];
    }

    /**
     * @return array<string, array{label: string, type: string}>
     */
    private static function flatFields(): array
    {
        return [
            'number' => ['label' => 'Wohnungsnummer', 'type' => 'text'],
            'description' => ['label' => 'Beschreibung', 'type' => 'textarea'],
            'size_sqm' => ['label' => 'Größe (m²)', 'type' => 'number'],
            'rent_cold' => ['label' => 'Kaltmiete', 'type' => 'number'],
            'utility_cost' => ['label' => 'Nebenkosten', 'type' => 'number'],
            'total_rent' => ['label' => 'Gesamtmiete', 'type' => 'number'],
            'floor' => ['label' => 'Etage', 'type' => 'number'],
            'is_wheelchair_accessible' => ['label' => 'Barrierefrei', 'type' => 'checkbox'],
            // Related rental object fields
            'rental_object.name' => ['label' => 'Objekt: Name', 'type' => 'text'],
            'rental_object.street' => ['label' => 'Objekt: Straße', 'type' => 'text'],
            'rental_object.street_number' => ['label' => 'Objekt: Hausnummer', 'type' => 'text'],
            'rental_object.postal_code' => ['label' => 'Objekt: PLZ', 'type' => 'text'],
            'rental_object.city' => ['label' => 'Objekt: Stadt', 'type' => 'text'],
        ];
    }

    /**
     * Get value from a model using dot notation for nested properties.
     */
    public static function getValueFromModel(Model $model, string $field): mixed
    {
        // Handle computed properties
        if ($field === 'full_address' && method_exists($model, 'fullAddress')) {
            return $model->fullAddress();
        }

        if ($field === 'full_name' && method_exists($model, 'fullName')) {
            return $model->fullName();
        }

        if ($field === 'total_rent' && method_exists($model, 'totalRent')) {
            return $model->totalRent();
        }

        // Handle dot notation for relationships
        if (str_contains($field, '.')) {
            $parts = explode('.', $field, 2);
            $relation = $parts[0];
            $attribute = $parts[1];

            if (method_exists($model, $relation)) {
                $related = $model->{$relation};
                if ($related) {
                    return self::getValueFromModel($related, $attribute);
                }
            }

            return null;
        }

        // Direct attribute access
        return $model->{$field} ?? null;
    }

    /**
     * Get auto-fill values for a form based on field mappings.
     *
     * @param  \Illuminate\Support\Collection  $fields
     * @return array<string, mixed>
     */
    public static function getAutoFillValues($fields, ?Model $source): array
    {
        if (! $source) {
            return [];
        }

        $values = [];

        foreach ($fields as $field) {
            $sourceField = $field->getConfig('autofill_field');

            if (! $sourceField) {
                continue;
            }

            $value = self::getValueFromModel($source, $sourceField);

            if ($value !== null) {
                // Format value based on field type
                $values[$field->name] = self::formatValue($value, $field->type->value);
            }
        }

        return $values;
    }

    /**
     * Format a value for display based on field type.
     */
    private static function formatValue(mixed $value, string $fieldType): mixed
    {
        if ($value === null) {
            return '';
        }

        return match ($fieldType) {
            'checkbox' => (bool) $value,
            'number' => is_numeric($value) ? $value : 0,
            'date' => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value,
            default => (string) $value,
        };
    }

    /**
     * Resolve the source model from context.
     */
    public static function resolveSource(string $sourceType, ?string $sourceId): ?Model
    {
        if (! $sourceId) {
            return null;
        }

        return match ($sourceType) {
            'listing' => Listing::find($sourceId),
            'listing_request' => ListingRequest::with('listing')->find($sourceId),
            'flat' => Flat::with('rentalObject')->find($sourceId),
            default => null,
        };
    }
}

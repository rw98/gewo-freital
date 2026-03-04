<?php

namespace App\Models;

use App\Enums\FormFieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    /** @use HasFactory<\Database\Factories\FormFieldFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_id',
        'parent_id',
        'column_index',
        'type',
        'name',
        'label',
        'description',
        'placeholder',
        'config',
        'validation_rules',
        'order',
        'is_required',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => FormFieldType::class,
            'config' => 'array',
            'validation_rules' => 'array',
            'is_required' => 'boolean',
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
     * @return BelongsTo<FormField, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'parent_id');
    }

    /**
     * @return HasMany<FormField, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(FormField::class, 'parent_id')->orderBy('column_index')->orderBy('order');
    }

    /**
     * Get children for a specific column index.
     *
     * @return HasMany<FormField, $this>
     */
    public function childrenInColumn(int $columnIndex): HasMany
    {
        return $this->children()->where('column_index', $columnIndex);
    }

    /**
     * @return HasMany<FormFieldValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(FormFieldValue::class);
    }

    /**
     * Get the validation rules for this field.
     *
     * @return array<int, string>
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add type-specific default rules
        $defaultRules = $this->type->defaultValidationRules();
        $rules = array_merge($rules, $defaultRules);

        // Add custom validation rules from config
        if (! empty($this->validation_rules)) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        return $rules;
    }

    /**
     * Get a config value with optional default.
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}

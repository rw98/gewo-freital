<?php

namespace App\Enums;

enum FormFieldType: string
{
    case Text = 'text';
    case Email = 'email';
    case Textarea = 'textarea';
    case Select = 'select';
    case Radio = 'radio';
    case Checkbox = 'checkbox';
    case Date = 'date';
    case File = 'file';
    case Number = 'number';
    case Phone = 'phone';
    case Info = 'info';
    case Row = 'row';

    public function label(): string
    {
        return __('enums.form_field_type.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'pencil',
            self::Email => 'envelope',
            self::Textarea => 'document-text',
            self::Select => 'chevron-up-down',
            self::Radio => 'list-bullet',
            self::Checkbox => 'check-circle',
            self::Date => 'calendar',
            self::File => 'paper-clip',
            self::Number => 'hashtag',
            self::Phone => 'phone',
            self::Info => 'information-circle',
            self::Row => 'view-columns',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        return match ($this) {
            self::Text => ['min_length' => null, 'max_length' => 255],
            self::Email => [],
            self::Textarea => ['rows' => 4, 'max_length' => 5000],
            self::Select => ['options' => [], 'multiple' => false],
            self::Radio => ['options' => []],
            self::Checkbox => ['default_checked' => false],
            self::Date => ['min_date' => null, 'max_date' => null],
            self::File => ['allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png'], 'max_size_kb' => 10240],
            self::Number => ['min' => null, 'max' => null, 'step' => 1],
            self::Phone => [],
            self::Info => ['style' => 'default', 'content' => ''],
            self::Row => ['columns' => [1, 1, 1]], // Array of column spans (total should equal 3)
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultValidationRules(): array
    {
        return match ($this) {
            self::Text => ['string', 'max:255'],
            self::Email => ['email', 'max:255'],
            self::Textarea => ['string', 'max:5000'],
            self::Select => ['string'],
            self::Radio => ['string'],
            self::Checkbox => ['boolean'],
            self::Date => ['date'],
            self::File => ['file', 'max:10240'],
            self::Number => ['numeric'],
            self::Phone => ['string', 'max:50'],
            self::Info => [],
            self::Row => [],
        };
    }

    /**
     * Check if this field type is a layout container.
     */
    public function isLayoutContainer(): bool
    {
        return $this === self::Row;
    }

    /**
     * Check if this field type requires user input.
     */
    public function requiresInput(): bool
    {
        return ! in_array($this, [self::Info, self::Row]);
    }
}

<?php

namespace App\Enums;

enum TemplateCategory: string
{
    case Landing = 'landing';
    case About = 'about';
    case Contact = 'contact';
    case Legal = 'legal';
    case Content = 'content';

    public function label(): string
    {
        return __('enums.template_category.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Landing => 'rocket-launch',
            self::About => 'user-group',
            self::Contact => 'envelope',
            self::Legal => 'scale',
            self::Content => 'document-text',
        };
    }
}

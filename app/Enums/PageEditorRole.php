<?php

namespace App\Enums;

enum PageEditorRole: string
{
    case Editor = 'editor';
    case Admin = 'admin';

    public function label(): string
    {
        return __('enums.page_editor_role.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Editor => 'blue',
            self::Admin => 'purple',
        };
    }
}

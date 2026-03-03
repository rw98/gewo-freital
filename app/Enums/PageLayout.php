<?php

namespace App\Enums;

enum PageLayout: string
{
    case Default = 'default';
    case FullWidth = 'full_width';
    case Sidebar = 'sidebar';
    case Landing = 'landing';

    public function label(): string
    {
        return __('enums.page_layout.'.$this->value);
    }
}

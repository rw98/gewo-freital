<?php

namespace App\Enums;

enum BlockType: string
{
    // Text
    case Heading = 'heading';
    case Paragraph = 'paragraph';
    case RichText = 'rich_text';

    // Media
    case Image = 'image';
    case ImageGallery = 'image_gallery';
    case Video = 'video';

    // Layout
    case Grid = 'grid';
    case Columns = 'columns';
    case Spacer = 'spacer';
    case Divider = 'divider';

    // Content
    case Card = 'card';
    case Callout = 'callout';
    case ListBlock = 'list';
    case Table = 'table';
    case Accordion = 'accordion';
    case Tabs = 'tabs';

    // Interactive
    case Button = 'button';
    case ButtonGroup = 'button_group';
    case ContactForm = 'contact_form';

    // Pre-built Sections
    case Hero = 'hero';
    case FeatureGrid = 'feature_grid';
    case Testimonials = 'testimonials';
    case Faq = 'faq';
    case Cta = 'cta';
    case PricingTable = 'pricing_table';

    public function label(): string
    {
        return __('enums.block_type.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Heading => 'h1',
            self::Paragraph => 'bars-3-bottom-left',
            self::RichText => 'document-text',
            self::Image => 'photo',
            self::ImageGallery => 'squares-2x2',
            self::Video => 'play-circle',
            self::Grid => 'squares-plus',
            self::Columns => 'view-columns',
            self::Spacer => 'arrows-up-down',
            self::Divider => 'minus',
            self::Card => 'rectangle-stack',
            self::Callout => 'chat-bubble-left',
            self::ListBlock => 'list-bullet',
            self::Table => 'table-cells',
            self::Accordion => 'chevron-down',
            self::Tabs => 'rectangle-group',
            self::Button => 'cursor-arrow-rays',
            self::ButtonGroup => 'squares-2x2',
            self::ContactForm => 'envelope',
            self::Hero => 'sparkles',
            self::FeatureGrid => 'view-columns',
            self::Testimonials => 'chat-bubble-bottom-center-text',
            self::Faq => 'question-mark-circle',
            self::Cta => 'megaphone',
            self::PricingTable => 'credit-card',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::Heading, self::Paragraph, self::RichText => 'text',
            self::Image, self::ImageGallery, self::Video => 'media',
            self::Grid, self::Columns, self::Spacer, self::Divider => 'layout',
            self::Card, self::Callout, self::ListBlock, self::Table, self::Accordion, self::Tabs => 'content',
            self::Button, self::ButtonGroup, self::ContactForm => 'interactive',
            self::Hero, self::FeatureGrid, self::Testimonials, self::Faq, self::Cta, self::PricingTable => 'sections',
        };
    }

    /**
     * @return array<string, BlockType[]>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::cases() as $type) {
            $groups[$type->category()][] = $type;
        }

        return $groups;
    }

    public function supportsChildren(): bool
    {
        return match ($this) {
            self::Grid, self::Columns, self::Accordion, self::Tabs => true,
            default => false,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultContent(): array
    {
        return match ($this) {
            self::Heading => ['text' => '', 'level' => 2],
            self::Paragraph => ['text' => ''],
            self::RichText => ['html' => ''],
            self::Image => ['src' => '', 'alt' => '', 'caption' => ''],
            self::ImageGallery => ['images' => []],
            self::Video => ['url' => '', 'provider' => 'youtube'],
            self::Grid => ['columns' => 3, 'gap' => 4],
            self::Columns => ['layout' => '1/2-1/2'],
            self::Spacer => ['height' => 'md'],
            self::Divider => ['style' => 'solid'],
            self::Card => ['title' => '', 'content' => '', 'image' => ''],
            self::Callout => ['type' => 'info', 'title' => '', 'content' => ''],
            self::ListBlock => ['items' => [], 'style' => 'bullet'],
            self::Table => ['headers' => [], 'rows' => []],
            self::Accordion => ['items' => []],
            self::Tabs => ['tabs' => []],
            self::Button => ['text' => '', 'url' => '', 'variant' => 'primary', 'size' => 'base'],
            self::ButtonGroup => ['buttons' => []],
            self::ContactForm => ['recipient_email' => '', 'success_message' => ''],
            self::Hero => ['heading' => '', 'subheading' => '', 'image' => '', 'cta_text' => '', 'cta_url' => ''],
            self::FeatureGrid => ['features' => []],
            self::Testimonials => ['testimonials' => []],
            self::Faq => ['items' => []],
            self::Cta => ['heading' => '', 'content' => '', 'button_text' => '', 'button_url' => ''],
            self::PricingTable => ['plans' => []],
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultSettings(): array
    {
        return [
            'padding' => 'none',
            'margin' => 'none',
            'background' => 'transparent',
            'text_align' => 'left',
        ];
    }
}

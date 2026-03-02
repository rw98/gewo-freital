<?php

use Livewire\Attributes\Session;
use Livewire\Component;

new class extends Component {
    #[Session]
    public string $fontSize = 'normal';

    public function setFontSize(string $size): void
    {
        $this->fontSize = $size;
        $this->dispatch('font-size-changed', size: $size);
    }
};
?>

<div
    x-data="{ fontSize: @entangle('fontSize') }"
    x-init="$watch('fontSize', value => document.documentElement.setAttribute('data-font-size', value))"
    x-effect="document.documentElement.setAttribute('data-font-size', fontSize)"
>
    <flux:radio.group
        wire:model.live="fontSize"
        variant="segmented"
        size="sm"
    >
        <flux:radio value="small" icon="a-arrow-down"/>
        <flux:radio value="normal" icon="a-large-small"/>
        <flux:radio value="large" icon="a-arrow-up"/>
    </flux:radio.group>
</div>

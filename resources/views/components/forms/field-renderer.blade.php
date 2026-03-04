@props(['field', 'name', 'locked' => false])

@php
    use App\Enums\FormFieldType;
@endphp

<flux:field>
    @switch($field->type)
        @case(FormFieldType::Text)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="text"
                :placeholder="$field->placeholder"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::Email)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="email"
                :placeholder="$field->placeholder"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::Textarea)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:textarea
                :rows="$field->getConfig('rows', 4)"
                :placeholder="$field->placeholder"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::Select)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:select :placeholder="$field->placeholder ?? __('forms.field.select_placeholder')" :disabled="$locked" {{ $attributes }}>
                @foreach ($field->getConfig('options', []) as $option)
                    <flux:select.option :value="$option['value'] ?? $option['label']">
                        {{ $option['label'] }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            @break

        @case(FormFieldType::Radio)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:radio.group :disabled="$locked" {{ $attributes }}>
                @foreach ($field->getConfig('options', []) as $option)
                    <flux:radio :value="$option['value'] ?? $option['label']" :label="$option['label']" />
                @endforeach
            </flux:radio.group>
            @break

        @case(FormFieldType::Checkbox)
            <div class="flex items-center gap-2">
                <flux:checkbox
                    :label="$field->label"
                    :disabled="$locked"
                    {{ $attributes }}
                />
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500" />
                @endif
            </div>
            @break

        @case(FormFieldType::Date)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="date"
                :min="$field->getConfig('min_date')"
                :max="$field->getConfig('max_date')"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::File)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="file"
                {{ $attributes }}
            />
            @php
                $extensions = $field->getConfig('allowed_extensions', []);
                $maxSize = $field->getConfig('max_size_kb', 10240);
            @endphp
            @if (!empty($extensions) || $maxSize)
                <flux:description>
                    @if (!empty($extensions))
                        {{ __('forms.field.allowed_types') }}: {{ implode(', ', $extensions) }}.
                    @endif
                    @if ($maxSize)
                        {{ __('forms.field.max_size') }}: {{ round($maxSize / 1024, 1) }} MB
                    @endif
                </flux:description>
            @endif
            @break

        @case(FormFieldType::Number)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="number"
                :min="$field->getConfig('min')"
                :max="$field->getConfig('max')"
                :step="$field->getConfig('step', 1)"
                :placeholder="$field->placeholder"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::Phone)
            <flux:label>
                {{ $field->label }}
                @if ($field->is_required)
                    <span class="text-red-500">*</span>
                @endif
                @if ($locked)
                    <flux:icon name="lock-closed" class="size-3 text-blue-500 inline ml-1" />
                @endif
            </flux:label>
            <flux:input
                type="tel"
                :placeholder="$field->placeholder"
                :disabled="$locked"
                :class="$locked ? 'bg-blue-50' : ''"
                {{ $attributes }}
            />
            @break

        @case(FormFieldType::Info)
            @php
                $style = $field->getConfig('style', 'default');
                $content = $field->getConfig('content');
                $variant = match($style) {
                    'info' => 'info',
                    'warning' => 'warning',
                    'success' => 'success',
                    default => null,
                };
            @endphp
            @if ($variant)
                <flux:callout :variant="$variant">
                    @if ($field->label)
                        <flux:heading size="sm">{{ $field->label }}</flux:heading>
                    @endif
                    @if ($content)
                        <div class="prose prose-sm max-w-none">{!! $content !!}</div>
                    @elseif ($field->description)
                        <flux:text>{{ $field->description }}</flux:text>
                    @endif
                </flux:callout>
            @else
                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                    @if ($field->label)
                        <flux:heading size="sm">{{ $field->label }}</flux:heading>
                    @endif
                    @if ($content)
                        <div class="prose prose-sm max-w-none mt-1">{!! $content !!}</div>
                    @elseif ($field->description)
                        <flux:text class="mt-1">{{ $field->description }}</flux:text>
                    @endif
                </div>
            @endif
            @break
    @endswitch

    @if ($field->description)
        <flux:description>{{ $field->description }}</flux:description>
    @endif

    <flux:error :name="$attributes->wire('model')->value()" />
</flux:field>

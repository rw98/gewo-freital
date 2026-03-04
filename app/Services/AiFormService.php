<?php

namespace App\Services;

use App\Enums\FormFieldType;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;

class AiFormService
{
    /**
     * Analyze a PDF and generate form field definitions.
     *
     * @return array<int, array{type: string, name: string, label: string, description: string|null, placeholder: string|null, is_required: bool, config: array}>
     */
    public function analyzeAndGenerateFields(string $pdfPath, ?string $additionalPrompt = null): array
    {
        // Extract text from PDF
        $pdfText = $this->extractPdfText($pdfPath);

        if (empty(trim($pdfText))) {
            throw new \RuntimeException(__('forms.ai.pdf_empty'));
        }

        // Build prompt and call AI
        $prompt = $this->buildPrompt($pdfText, $additionalPrompt);

        $provider = config('services.ai.provider', 'anthropic');

        $text = match ($provider) {
            'openai' => $this->callOpenAI($prompt),
            default => $this->callAnthropic($prompt),
        };

        return $this->parseResponse($text);
    }

    /**
     * Extract text content from a PDF file.
     */
    private function extractPdfText(string $pdfPath): string
    {
        // Try pdftotext first (handles secured PDFs)
        $text = $this->extractWithPdfToText($pdfPath);

        if (! empty(trim($text))) {
            return $text;
        }

        // Fall back to PHP parser
        $parser = new Parser;
        $pdf = $parser->parseFile($pdfPath);

        return $pdf->getText();
    }

    /**
     * Extract text using pdftotext command (handles secured PDFs).
     */
    private function extractWithPdfToText(string $pdfPath): string
    {
        $pdftotext = '/opt/homebrew/bin/pdftotext';

        if (! file_exists($pdftotext)) {
            $pdftotext = 'pdftotext'; // Try system PATH
        }

        $outputFile = sys_get_temp_dir().'/'.uniqid('pdf_', true).'.txt';

        $command = sprintf(
            '%s -layout %s %s 2>/dev/null',
            escapeshellcmd($pdftotext),
            escapeshellarg($pdfPath),
            escapeshellarg($outputFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($outputFile)) {
            $text = file_get_contents($outputFile);
            unlink($outputFile);

            return $text;
        }

        return '';
    }

    /**
     * Build the AI prompt for form field generation.
     */
    private function buildPrompt(string $pdfText, ?string $additionalPrompt): string
    {
        $fieldTypes = collect(FormFieldType::cases())
            ->map(fn ($type) => "- {$type->value}: {$type->label()}")
            ->implode("\n");

        $additionalInstructions = $additionalPrompt
            ? "\n\nADDITIONAL USER INSTRUCTIONS:\n{$additionalPrompt}"
            : '';

        return <<<PROMPT
You are an expert form designer. Analyze the following PDF content and extract all form fields that should be created to digitize this form.

PDF CONTENT:
{$pdfText}

AVAILABLE FIELD TYPES:
{$fieldTypes}

RULES:
1. Identify all form fields, questions, or data collection points from the PDF
2. For each field, determine the most appropriate field type
3. Generate descriptive labels in German
4. Generate helpful placeholders in German where appropriate
5. Mark fields as required if they appear mandatory (marked with *, required text, etc.)
6. Use semantic field names (snake_case, e.g., "first_name", "date_of_birth")
7. Preserve the order of fields as they appear in the document

IMPORTANT - Mutually exclusive options:
- When you see options like "male/female", "yes/no", "married/single/divorced", or any set of mutually exclusive choices, create ONE "radio" or "select" field with all options - NOT separate fields for each option!
- Use "radio" for 2-5 visible options (e.g., gender, yes/no questions)
- Use "select" (dropdown) for more than 5 options or when space is limited
- Example: "Gender: [ ] Male [ ] Female [ ] Other" should become ONE radio field with 3 options

LAYOUT - Group related fields:
- Use "row" to group 1-3 related fields that should appear on the same line
- Common groupings: first_name + last_name, street + house_number, postal_code + city, phone + email
- Row value should be a number (1, 2, 3...) - fields with the same row number appear together
{$additionalInstructions}

INFO FIELDS - Static text without input:
- Use "info" type for section headers, instructions, legal notices, or any static text that doesn't require user input
- Info fields have a "content" in config for the text to display
- Use "style" in config: "default", "info" (blue), "warning" (yellow), or "success" (green)

OUTPUT FORMAT:
Return ONLY a JSON array with this structure:
```json
[
  {
    "type": "text|email|textarea|select|radio|checkbox|date|file|number|phone|info",
    "name": "field_name_snake_case",
    "label": "German Label",
    "description": "Optional helper text in German or null",
    "placeholder": "Placeholder text in German or null",
    "is_required": true,
    "row": 1,
    "config": {
      "options": [{"label": "Option 1", "value": "option_1"}]
    }
  }
]
```

The config object should only include relevant settings for each field type:
- select: include "options" array with label/value pairs
- radio: include "options" array with label/value pairs
- info: include "content" (HTML text) and "style" ("default", "info", "warning", "success")
- textarea: include "rows" (default 4)
- number: include "min", "max", "step" if applicable
- date: include "min_date", "max_date" if applicable
- text: include "max_length" if applicable

Return ONLY the JSON array, no additional text or explanation.
PROMPT;
    }

    /**
     * Call Anthropic API.
     */
    private function callAnthropic(string $prompt): string
    {
        $apiKey = config('services.anthropic.api_key');
        $model = config('services.anthropic.model', 'claude-sonnet-4-20250514');

        if (empty($apiKey)) {
            throw new \RuntimeException(__('forms.ai.api_key_missing'));
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => 8192,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('forms.ai.failed').': '.$response->body());
        }

        $result = $response->json();

        return $result['content'][0]['text'] ?? '';
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(string $prompt): string
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model', 'gpt-4o');

        if (empty($apiKey)) {
            throw new \RuntimeException(__('forms.ai.api_key_missing'));
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'max_tokens' => 8192,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert form designer. Always respond with valid JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('forms.ai.failed').': '.$response->body());
        }

        $result = $response->json();

        return $result['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Parse AI response into field definitions.
     *
     * @return array<int, array{type: string, name: string, label: string, description: string|null, placeholder: string|null, is_required: bool, config: array}>
     */
    private function parseResponse(string $text): array
    {
        $text = trim($text);

        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $match)) {
            $text = trim($match[1]);
        }

        // Try to find JSON array
        if (preg_match('/\[[\s\S]*\]/', $text, $match)) {
            $text = $match[0];
        }

        $result = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($result)) {
            throw new \RuntimeException(__('forms.ai.parse_failed'));
        }

        // Validate and normalize fields
        return collect($result)->map(function ($field) {
            // Validate field type
            $type = FormFieldType::tryFrom($field['type'] ?? 'text');
            if (! $type) {
                $type = FormFieldType::Text;
            }

            // Merge row into config if present
            $config = $field['config'] ?? $type->defaultConfig();
            if (isset($field['row'])) {
                $config['row'] = (int) $field['row'];
            }

            return [
                'type' => $type->value,
                'name' => $field['name'] ?? 'field_'.uniqid(),
                'label' => $field['label'] ?? 'Feld',
                'description' => $field['description'] ?? null,
                'placeholder' => $field['placeholder'] ?? null,
                'is_required' => $field['is_required'] ?? false,
                'config' => $config,
            ];
        })->toArray();
    }
}

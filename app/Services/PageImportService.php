<?php

namespace App\Services;

use App\Enums\BlockType;
use Illuminate\Support\Facades\Http;

class PageImportService
{
    /**
     * Fetch content from a URL and convert to markdown-like text.
     *
     * @return array{title: string, content: string, url: string}
     */
    public function fetchContent(string $url): array
    {
        $response = Http::timeout(30)->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException(__('pages.import.fetch_failed'));
        }

        $html = $response->body();

        // Extract title
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
        $title = isset($titleMatch[1]) ? trim(html_entity_decode($titleMatch[1])) : '';

        // Extract main content (try common content selectors)
        $content = $this->extractMainContent($html);

        return [
            'title' => $title,
            'content' => $content,
            'url' => $url,
        ];
    }

    /**
     * Extract main content from HTML.
     */
    private function extractMainContent(string $html): string
    {
        // Remove script and style tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<nav\b[^>]*>(.*?)<\/nav>/is', '', $html);
        $html = preg_replace('/<footer\b[^>]*>(.*?)<\/footer>/is', '', $html);
        $html = preg_replace('/<header\b[^>]*>(.*?)<\/header>/is', '', $html);

        // Try to find main content area
        $contentPatterns = [
            '/<main[^>]*>(.*?)<\/main>/is',
            '/<article[^>]*>(.*?)<\/article>/is',
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/is',
            '/<body[^>]*>(.*?)<\/body>/is',
        ];

        $content = '';
        foreach ($contentPatterns as $pattern) {
            if (preg_match($pattern, $html, $match)) {
                $content = $match[1];
                break;
            }
        }

        if (empty($content)) {
            $content = $html;
        }

        // Convert to readable text while preserving structure
        $content = $this->htmlToStructuredText($content);

        return trim($content);
    }

    /**
     * Convert HTML to structured text for AI processing.
     */
    private function htmlToStructuredText(string $html): string
    {
        // Preserve headings with markers
        $html = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', "\n# $1\n", $html);
        $html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n## $1\n", $html);
        $html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n### $1\n", $html);
        $html = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n#### $1\n", $html);
        $html = preg_replace('/<h5[^>]*>(.*?)<\/h5>/is', "\n##### $1\n", $html);
        $html = preg_replace('/<h6[^>]*>(.*?)<\/h6>/is', "\n###### $1\n", $html);

        // Preserve paragraphs
        $html = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "\n$1\n", $html);

        // Preserve list items
        $html = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "- $1\n", $html);

        // Preserve line breaks
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

        // Preserve links
        $html = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/is', '[$2]($1)', $html);

        // Preserve images
        $html = preg_replace('/<img[^>]*src="([^"]*)"[^>]*alt="([^"]*)"[^>]*\/?>/is', '[Image: $2]($1)', $html);
        $html = preg_replace('/<img[^>]*src="([^"]*)"[^>]*\/?>/is', '[Image]($1)', $html);

        // Strip remaining tags
        $html = strip_tags($html);

        // Clean up whitespace
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        $html = preg_replace('/[ \t]+/', ' ', $html);

        return html_entity_decode(trim($html));
    }

    /**
     * Generate page blocks from content using AI.
     *
     * @return array<int, array<string, mixed>>
     */
    public function generateBlocks(string $content, string $title, string $url): array
    {
        $apiKey = config('services.anthropic.api_key');
        $model = config('services.anthropic.model');

        if (empty($apiKey)) {
            throw new \RuntimeException(__('pages.import.api_key_missing'));
        }

        $blockTypes = $this->getAvailableBlockTypes();

        $prompt = $this->buildPrompt($content, $title, $url, $blockTypes);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(__('pages.import.ai_failed').': '.$response->body());
        }

        $result = $response->json();
        $text = $result['content'][0]['text'] ?? '';

        return $this->parseAiResponse($text);
    }

    /**
     * Get available block types with their content structure.
     */
    private function getAvailableBlockTypes(): string
    {
        return <<<'TYPES'
Available block types and their content structure:

1. heading - For titles and section headers
   content: { "text": "string", "level": 1-6 }

2. paragraph - For text paragraphs
   content: { "text": "string" }

3. rich_text - For formatted HTML content
   content: { "html": "html string with <p>, <strong>, <em>, <ul>, <li>, <a> tags" }

4. image - For images
   content: { "src": "url", "alt": "description", "caption": "optional caption" }

5. list - For bullet or numbered lists
   content: { "items": ["item1", "item2"], "style": "bullet" or "ordered" }

6. callout - For highlighted information boxes
   content: { "type": "info" or "warning" or "success" or "error", "title": "string", "content": "string" }

7. card - For content cards
   content: { "title": "string", "content": "string", "image": "optional url" }

8. divider - For visual separation
   content: { "style": "solid" or "dashed" or "dotted" }

9. spacer - For vertical spacing
   content: { "height": "sm" or "md" or "lg" }

10. button - For call-to-action buttons
    content: { "text": "string", "url": "string", "variant": "primary" or "secondary" or "ghost", "size": "sm" or "base" or "lg" }

11. hero - For hero sections
    content: { "heading": "string", "subheading": "string", "image": "url", "cta_text": "string", "cta_url": "string" }

12. cta - For call-to-action sections
    content: { "heading": "string", "content": "string", "button_text": "string", "button_url": "string" }

13. faq - For FAQ sections
    content: { "items": [{ "question": "string", "answer": "string" }] }

14. feature_grid - For feature listings
    content: { "features": [{ "icon": "heroicon name", "title": "string", "description": "string" }] }
TYPES;
    }

    /**
     * Build the AI prompt.
     */
    private function buildPrompt(string $content, string $title, string $url, string $blockTypes): string
    {
        return <<<PROMPT
You are a web page content analyzer. Your task is to convert the following web page content into a structured page layout using the available block types.

Source URL: {$url}
Page Title: {$title}

{$blockTypes}

CONTENT TO ANALYZE:
---
{$content}
---

INSTRUCTIONS:
1. Analyze the content and create an appropriate page layout
2. Use heading blocks for titles and section headers (use appropriate levels: h1 for main title, h2 for sections, etc.)
3. Use paragraph or rich_text blocks for body text
4. Use list blocks when you find lists
5. Use callout blocks for important notices or highlighted information
6. Use divider or spacer blocks to separate sections
7. If there's a clear hero section or main heading, consider using a hero block
8. Use appropriate image blocks if images are referenced
9. Create a logical flow from top to bottom

OUTPUT FORMAT:
Return ONLY a valid JSON array of blocks. Each block must have:
- "type": one of the block type names (e.g., "heading", "paragraph")
- "content": object matching the block type's content structure
- "column_span": 12 (full width) unless you want narrower content

Example output format:
```json
[
  {"type": "heading", "content": {"text": "Welcome", "level": 1}, "column_span": 12},
  {"type": "paragraph", "content": {"text": "Introduction text here."}, "column_span": 12}
]
```

Return ONLY the JSON array, no additional text or explanation.
PROMPT;
    }

    /**
     * Parse AI response to extract blocks.
     *
     * @return array<int, array<string, mixed>>
     */
    private function parseAiResponse(string $text): array
    {
        // Try to extract JSON from the response
        $text = trim($text);

        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $match)) {
            $text = trim($match[1]);
        }

        // Try to find JSON array
        if (preg_match('/\[[\s\S]*\]/', $text, $match)) {
            $text = $match[0];
        }

        $blocks = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($blocks)) {
            throw new \RuntimeException(__('pages.import.parse_failed'));
        }

        // Validate and clean blocks
        return $this->validateBlocks($blocks);
    }

    /**
     * Validate and clean block data.
     *
     * @param  array<int, mixed>  $blocks
     * @return array<int, array<string, mixed>>
     */
    private function validateBlocks(array $blocks): array
    {
        $validBlocks = [];
        $validTypes = array_map(fn ($case) => $case->value, BlockType::cases());

        foreach ($blocks as $block) {
            if (! is_array($block) || ! isset($block['type'])) {
                continue;
            }

            $type = $block['type'];

            if (! in_array($type, $validTypes)) {
                continue;
            }

            $blockType = BlockType::from($type);

            $validBlocks[] = [
                'type' => $type,
                'content' => array_merge(
                    $blockType->defaultContent(),
                    $block['content'] ?? []
                ),
                'settings' => $blockType->defaultSettings(),
                'column_span' => min(12, max(1, (int) ($block['column_span'] ?? 12))),
            ];
        }

        return $validBlocks;
    }
}

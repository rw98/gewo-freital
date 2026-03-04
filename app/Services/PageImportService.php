<?php

namespace App\Services;

use App\Enums\BlockType;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class PageImportService
{
    /**
     * Fetch content from a URL and convert to markdown-like text.
     *
     * @return array{title: string, content: string, url: string, images: array<int, array{src: string, alt: string}>, tables: array<int, array{headers: array<string>, rows: array<array<string>>}>, layoutSections: array<int, array<string, mixed>>}
     *
     * @throws ConnectionException
     */
    public function fetchContent(string $url): array
    {
        $response = Http::timeout(30)->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException(__('pages.import.fetch_failed'));
        }

        $html = $response->body();
        $baseUrl = $this->getBaseUrl($url);

        // Extract title
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
        $title = isset($titleMatch[1]) ? trim(html_entity_decode($titleMatch[1])) : '';

        // Extract images with full URLs
        $images = $this->extractImages($html, $baseUrl);

        // Extract tables
        $tables = $this->extractTables($html);

        // Extract layout sections (grids, cards, columns, etc.)
        $layoutSections = $this->extractLayoutSections($html, $baseUrl);

        // Extract main content (try common content selectors)
        $content = $this->extractMainContent($html, $baseUrl);

        return [
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'images' => $images,
            'tables' => $tables,
            'layoutSections' => $layoutSections,
        ];
    }

    /**
     * Extract layout sections from HTML to understand the page structure.
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractLayoutSections(string $html, string $baseUrl): array
    {
        $sections = [];

        // Remove scripts/styles first
        $cleanHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $cleanHtml = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $cleanHtml);

        // Detect hero sections (large images with text overlay, or full-width banners)
        if (preg_match('/<(?:section|div)[^>]*(?:class="[^"]*(?:hero|banner|jumbotron|cover)[^"]*")[^>]*>(.*?)<\/(?:section|div)>/is', $cleanHtml, $match)) {
            $heroContent = $match[1];
            $section = ['type' => 'hero', 'content' => []];

            // Extract heading
            if (preg_match('/<h[12][^>]*>(.*?)<\/h[12]>/is', $heroContent, $h)) {
                $section['content']['heading'] = trim(strip_tags($h[1]));
            }
            // Extract subheading/description
            if (preg_match('/<(?:p|h[3-6])[^>]*>(.*?)<\/(?:p|h[3-6])>/is', $heroContent, $p)) {
                $section['content']['subheading'] = trim(strip_tags($p[1]));
            }
            // Extract background image
            if (preg_match('/(?:background(?:-image)?:\s*url\(["\']?([^"\')\s]+)|<img[^>]*src=["\']([^"\']+))/i', $heroContent, $img)) {
                $imgSrc = $img[1] ?: $img[2];
                $section['content']['image'] = $this->resolveUrl($imgSrc, $baseUrl);
            }
            // Extract CTA button
            if (preg_match('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $heroContent, $cta)) {
                $section['content']['cta_url'] = $this->resolveUrl($cta[1], $baseUrl);
                $section['content']['cta_text'] = trim(strip_tags($cta[2]));
            }

            $sections[] = $section;
        }

        // Detect grid/card sections by finding repeated similar elements
        // This approach finds all card-like elements directly, regardless of container
        // Use word boundaries or specific suffixes to avoid matching containers like "features"
        $cardPatterns = [
            '/<(?:div|article)[^>]*class="[^"]*(?:card|feature-item|feature-card|service-item|product-item|team-member|grid-item)[^"]*"[^>]*>/',
            '/<(?:div|article)[^>]*class="[^"]*(?:col-\d|col-md-\d|col-lg-\d|col-sm-\d)[^"]*"[^>]*>/',
        ];

        foreach ($cardPatterns as $cardPattern) {
            // Find all card-like elements and their positions
            if (preg_match_all($cardPattern, $cleanHtml, $cardStartMatches, PREG_OFFSET_CAPTURE)) {
                $cardCount = count($cardStartMatches[0]);

                if ($cardCount >= 2 && $cardCount <= 6) {
                    $section = [
                        'type' => 'grid',
                        'columns' => $cardCount,
                        'items' => [],
                    ];

                    // Extract content from each card using position-based parsing
                    foreach ($cardStartMatches[0] as $index => $match) {
                        $startPos = $match[1];
                        $tagStart = $match[0];

                        // Determine if it's a div or article
                        $tagType = str_contains($tagStart, '<article') ? 'article' : 'div';

                        // Find the end of this element (simple approach: find next card or end)
                        if ($index < $cardCount - 1) {
                            $endPos = $cardStartMatches[0][$index + 1][1];
                        } else {
                            $endPos = strlen($cleanHtml);
                        }

                        $cardHtml = substr($cleanHtml, $startPos, $endPos - $startPos);
                        $item = [];

                        // Extract image
                        if (preg_match('/<img[^>]*src=["\']([^"\']+)["\'][^>]*(?:alt=["\']([^"\']*)["\'])?/i', $cardHtml, $img)) {
                            $item['image'] = $this->resolveUrl($img[1], $baseUrl);
                            $item['image_alt'] = $img[2] ?? '';
                        }

                        // Extract title (h2-h5)
                        if (preg_match('/<h[2-5][^>]*>(.*?)<\/h[2-5]>/is', $cardHtml, $title)) {
                            $item['title'] = trim(strip_tags($title[1]));
                        }

                        // Extract description (first paragraph)
                        if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $cardHtml, $desc)) {
                            $item['description'] = trim(strip_tags($desc[1]));
                        }

                        // Extract link
                        if (preg_match('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $cardHtml, $link)) {
                            $item['link_url'] = $this->resolveUrl($link[1], $baseUrl);
                            $item['link_text'] = trim(strip_tags($link[2]));
                        }

                        if (! empty($item)) {
                            $section['items'][] = $item;
                        }
                    }

                    if (! empty($section['items'])) {
                        $sections[] = $section;
                    }

                    break; // Only detect one grid section per pattern
                }
            }
        }

        // Detect two-column layouts (image + text, or text + text)
        $twoColPatterns = [
            '/<(?:div|section)[^>]*class="[^"]*(?:two-col|split|side-by-side|row)[^"]*"[^>]*>(.*?)<\/(?:div|section)>/is',
        ];

        foreach ($twoColPatterns as $pattern) {
            if (preg_match_all($pattern, $cleanHtml, $matches)) {
                foreach ($matches[1] as $colContent) {
                    // Check if it has exactly 2 main child divs
                    if (preg_match_all('/<(?:div)[^>]*class="[^"]*(?:col|column)[^"]*"[^>]*>(.*?)<\/div>/is', $colContent, $cols) && count($cols[1]) === 2) {
                        $section = [
                            'type' => 'columns',
                            'layout' => '1/2-1/2',
                            'items' => [],
                        ];

                        foreach ($cols[1] as $colHtml) {
                            $item = ['content_type' => 'text', 'content' => ''];

                            // Check if it's primarily an image
                            if (preg_match('/<img[^>]*src=["\']([^"\']+)["\'][^>]*(?:alt=["\']([^"\']*)["\'])?/i', $colHtml, $img)) {
                                $item['content_type'] = 'image';
                                $item['image'] = $this->resolveUrl($img[1], $baseUrl);
                                $item['image_alt'] = $img[2] ?? '';
                            }

                            // Extract text content
                            $textContent = trim(strip_tags(preg_replace('/<img[^>]*>/i', '', $colHtml)));
                            if (! empty($textContent)) {
                                if ($item['content_type'] !== 'image') {
                                    $item['content_type'] = 'text';
                                }
                                $item['content'] = mb_substr($textContent, 0, 500);
                            }

                            $section['items'][] = $item;
                        }

                        $sections[] = $section;
                    }
                }
            }
        }

        // Detect CTA sections
        if (preg_match('/<(?:section|div)[^>]*class="[^"]*(?:cta|call-to-action|action)[^"]*"[^>]*>(.*?)<\/(?:section|div)>/is', $cleanHtml, $match)) {
            $ctaContent = $match[1];
            $section = ['type' => 'cta', 'content' => []];

            if (preg_match('/<h[2-4][^>]*>(.*?)<\/h[2-4]>/is', $ctaContent, $h)) {
                $section['content']['heading'] = trim(strip_tags($h[1]));
            }
            if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $ctaContent, $p)) {
                $section['content']['description'] = trim(strip_tags($p[1]));
            }
            if (preg_match('/<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $ctaContent, $btn)) {
                $section['content']['button_url'] = $this->resolveUrl($btn[1], $baseUrl);
                $section['content']['button_text'] = trim(strip_tags($btn[2]));
            }

            if (! empty($section['content'])) {
                $sections[] = $section;
            }
        }

        // Detect FAQ sections
        if (preg_match('/<(?:section|div)[^>]*class="[^"]*(?:faq|accordion|questions)[^"]*"[^>]*>(.*?)<\/(?:section|div)>/is', $cleanHtml, $match)) {
            $faqContent = $match[1];

            // Try to find question/answer pairs
            if (preg_match_all('/<(?:dt|h[3-5]|button)[^>]*>(.*?)<\/(?:dt|h[3-5]|button)>\s*<(?:dd|div|p)[^>]*>(.*?)<\/(?:dd|div|p)>/is', $faqContent, $faqs)) {
                if (count($faqs[1]) >= 2) {
                    $section = ['type' => 'faq', 'items' => []];

                    for ($i = 0; $i < count($faqs[1]); $i++) {
                        $section['items'][] = [
                            'question' => trim(strip_tags($faqs[1][$i])),
                            'answer' => trim(strip_tags($faqs[2][$i])),
                        ];
                    }

                    $sections[] = $section;
                }
            }
        }

        return $sections;
    }

    /**
     * Extract tables from HTML.
     *
     * @return array<int, array{headers: array<string>, rows: array<array<string>>}>
     */
    private function extractTables(string $html): array
    {
        $tables = [];

        // Find all table elements
        preg_match_all('/<table[^>]*>(.*?)<\/table>/is', $html, $tableMatches);

        foreach ($tableMatches[1] as $tableHtml) {
            $headers = [];
            $rows = [];

            // Extract headers from thead or first tr with th elements
            if (preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $tableHtml, $theadMatch)) {
                preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $theadMatch[1], $thMatches);
                $headers = array_map(fn ($h) => trim(strip_tags(html_entity_decode($h))), $thMatches[1]);
            } elseif (preg_match('/<tr[^>]*>(.*?)<\/tr>/is', $tableHtml, $firstRow)) {
                if (preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $firstRow[1], $thMatches)) {
                    $headers = array_map(fn ($h) => trim(strip_tags(html_entity_decode($h))), $thMatches[1]);
                }
            }

            // Extract rows from tbody or all tr elements
            $rowsHtml = $tableHtml;
            if (preg_match('/<tbody[^>]*>(.*?)<\/tbody>/is', $tableHtml, $tbodyMatch)) {
                $rowsHtml = $tbodyMatch[1];
            }

            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $rowsHtml, $trMatches);

            foreach ($trMatches[1] as $trHtml) {
                // Skip header rows
                if (preg_match('/<th[^>]*>/i', $trHtml) && empty($rows)) {
                    // If we didn't find headers yet, extract them
                    if (empty($headers)) {
                        preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $trHtml, $thMatches);
                        $headers = array_map(fn ($h) => trim(strip_tags(html_entity_decode($h))), $thMatches[1]);
                    }

                    continue;
                }

                // Extract cells
                preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $trHtml, $tdMatches);
                if (! empty($tdMatches[1])) {
                    $row = array_map(fn ($c) => trim(strip_tags(html_entity_decode($c))), $tdMatches[1]);
                    $rows[] = $row;
                }
            }

            // Only add tables that have meaningful content
            if (! empty($headers) || ! empty($rows)) {
                $tables[] = [
                    'headers' => $headers,
                    'rows' => $rows,
                ];
            }
        }

        return $tables;
    }

    /**
     * Get base URL for resolving relative paths.
     */
    private function getBaseUrl(string $url): string
    {
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':'.$parsed['port'] : '';

        return $scheme.'://'.$host.$port;
    }

    /**
     * Resolve a potentially relative URL to an absolute URL.
     */
    private function resolveUrl(string $src, string $baseUrl): string
    {
        // Already absolute
        if (preg_match('/^https?:\/\//i', $src)) {
            return $src;
        }

        // Protocol-relative
        if (str_starts_with($src, '//')) {
            return 'https:'.$src;
        }

        // Data URI or invalid
        if (str_starts_with($src, 'data:') || empty($src)) {
            return '';
        }

        // Absolute path
        if (str_starts_with($src, '/')) {
            return $baseUrl.$src;
        }

        // Relative path
        return $baseUrl.'/'.$src;
    }

    /**
     * Extract images from HTML with full URLs.
     *
     * @return array<int, array{src: string, alt: string}>
     */
    private function extractImages(string $html, string $baseUrl): array
    {
        $images = [];

        // Match img tags
        preg_match_all('/<img[^>]+>/is', $html, $imgTags);

        foreach ($imgTags[0] as $imgTag) {
            // Extract src
            if (preg_match('/src=["\']([^"\']+)["\']/i', $imgTag, $srcMatch)) {
                $src = $this->resolveUrl($srcMatch[1], $baseUrl);

                if (empty($src)) {
                    continue;
                }

                // Extract alt
                $alt = '';
                if (preg_match('/alt=["\']([^"\']*)["\']/', $imgTag, $altMatch)) {
                    $alt = html_entity_decode($altMatch[1]);
                }

                // Skip tiny images (likely icons/tracking pixels)
                $isLikelyContent = true;
                if (preg_match('/width=["\']?(\d+)/i', $imgTag, $widthMatch)) {
                    if ((int) $widthMatch[1] < 50) {
                        $isLikelyContent = false;
                    }
                }

                if ($isLikelyContent) {
                    $images[] = [
                        'src' => $src,
                        'alt' => $alt,
                    ];
                }
            }
        }

        // Also check for background images in style attributes
        preg_match_all('/background(?:-image)?:\s*url\(["\']?([^"\')\s]+)["\']?\)/i', $html, $bgMatches);
        foreach ($bgMatches[1] as $bgSrc) {
            $src = $this->resolveUrl($bgSrc, $baseUrl);
            if (! empty($src)) {
                $images[] = [
                    'src' => $src,
                    'alt' => '',
                ];
            }
        }

        // Remove duplicates
        $unique = [];
        foreach ($images as $image) {
            $unique[$image['src']] = $image;
        }

        return array_values($unique);
    }

    /**
     * Extract main content from HTML.
     */
    private function extractMainContent(string $html, string $baseUrl): string
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
        $content = $this->htmlToStructuredText($content, $baseUrl);

        return trim($content);
    }

    /**
     * Convert HTML to structured text for AI processing.
     */
    private function htmlToStructuredText(string $html, string $baseUrl): string
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

        // Preserve links with absolute URLs
        $html = preg_replace_callback(
            '/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/is',
            fn ($m) => '['.$m[2].']('.$this->resolveUrl($m[1], $baseUrl).')',
            $html
        );

        // Preserve images with absolute URLs and clear markers
        $html = preg_replace_callback(
            '/<img[^>]*src="([^"]*)"[^>]*alt="([^"]*)"[^>]*\/?>/is',
            fn ($m) => "\n[IMAGE: ".$m[2].']('.$this->resolveUrl($m[1], $baseUrl).")\n",
            $html
        );
        $html = preg_replace_callback(
            '/<img[^>]*src="([^"]*)"[^>]*\/?>/is',
            fn ($m) => "\n[IMAGE](".$this->resolveUrl($m[1], $baseUrl).")\n",
            $html
        );

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
     * @param  array<int, array{src: string, alt: string}>  $images
     * @param  array<int, array{headers: array<string>, rows: array<array<string>>}>  $tables
     * @param  array<int, array<string, mixed>>  $layoutSections
     * @return array<int, array<string, mixed>>
     */
    public function generateBlocks(string $content, string $title, string $url, array $images = [], array $tables = [], array $layoutSections = []): array
    {
        $provider = config('services.ai.provider', 'anthropic');

        $blockTypes = $this->getAvailableBlockTypes();
        $prompt = $this->buildPrompt($content, $title, $url, $blockTypes, $images, $tables, $layoutSections);

        $text = match ($provider) {
            'openai' => $this->callOpenAI($prompt),
            default => $this->callAnthropic($prompt),
        };

        return $this->parseAiResponse($text);
    }

    /**
     * Call Anthropic API.
     */
    private function callAnthropic(string $prompt): string
    {
        $apiKey = config('services.anthropic.api_key');
        $model = config('services.anthropic.model');

        if (empty($apiKey)) {
            throw new \RuntimeException(__('pages.import.api_key_missing'));
        }

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

        return $result['content'][0]['text'] ?? '';
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(string $prompt): string
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.model');

        if (empty($apiKey)) {
            throw new \RuntimeException(__('pages.import.api_key_missing'));
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a web page content analyzer that converts HTML content into structured page blocks. Always respond with valid JSON only.',
                ],
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

        return $result['choices'][0]['message']['content'] ?? '';
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

4. image - For displaying images (USE THIS FOR ALL IMAGES!)
   content: { "src": "full image URL", "alt": "description", "caption": "optional caption" }

5. list - For bullet or numbered lists
   content: { "items": ["item1", "item2"], "style": "bullet" or "ordered" }

6. table - For data tables (USE THIS FOR ALL TABLES!)
   content: { "headers": ["Column1", "Column2", "Column3"], "rows": [["cell1", "cell2", "cell3"], ["cell4", "cell5", "cell6"]] }
   NOTE: headers is an array of column header strings, rows is an array of arrays where each inner array is a row of cell values

7. callout - For highlighted information boxes
   content: { "type": "info" or "warning" or "success" or "error", "title": "string", "content": "string" }

8. card - For content cards with optional image
   content: { "title": "string", "content": "string", "image": "optional image url" }

9. divider - For visual separation
   content: { "style": "solid" or "dashed" or "dotted" }

10. spacer - For vertical spacing
    content: { "height": "sm" or "md" or "lg" }

11. button - For call-to-action buttons
    content: { "text": "string", "url": "string", "variant": "primary" or "secondary" or "ghost", "size": "sm" or "base" or "lg" }

12. hero - For hero sections WITH background image
    content: { "heading": "string", "subheading": "string", "image": "background image URL", "cta_text": "string", "cta_url": "string" }

13. cta - For call-to-action sections
    content: { "heading": "string", "content": "string", "button_text": "string", "button_url": "string" }

14. faq - For FAQ sections
    content: { "items": [{ "question": "string", "answer": "string" }] }

15. feature_grid - For feature listings
    content: { "features": [{ "icon": "heroicon name", "title": "string", "description": "string" }] }

16. grid - For creating multi-column layouts with nested child blocks (USE FOR SIDE-BY-SIDE CONTENT!)
    content: { "columns": 2-6, "gap": 2-8 }
    children: [ array of child blocks that will be placed in the grid ]
    NOTE: Grid is a CONTAINER block. Use it to arrange content side by side.
    Example: To show 3 cards in a row, create a grid with columns: 3 and add 3 card blocks as children.
    Each child block has the same structure (type, content, column_span).
    Child column_span controls how many grid columns each child takes (1 = one column, 2 = two columns, etc.)

17. columns - For two-column layouts with specific proportions
    content: { "layout": "1/2-1/2" or "1/3-2/3" or "2/3-1/3" or "1/4-3/4" or "3/4-1/4" }
    children: [ array of child blocks ]
    NOTE: Similar to grid but for two-column layouts with specific proportions.
TYPES;
    }

    /**
     * Build the AI prompt.
     *
     * @param  array<int, array{src: string, alt: string}>  $images
     * @param  array<int, array{headers: array<string>, rows: array<array<string>>}>  $tables
     * @param  array<int, array<string, mixed>>  $layoutSections
     */
    private function buildPrompt(string $content, string $title, string $url, string $blockTypes, array $images, array $tables = [], array $layoutSections = []): string
    {
        $imageList = '';
        if (! empty($images)) {
            $imageList = "\n\nAVAILABLE IMAGES FROM THE PAGE:\n";
            foreach ($images as $index => $image) {
                $alt = $image['alt'] ?: 'No description';
                $imageList .= ($index + 1).". {$image['src']} (alt: {$alt})\n";
            }
        }

        $tableList = '';
        if (! empty($tables)) {
            $tableList = "\n\nTABLES EXTRACTED FROM THE PAGE:\n";
            foreach ($tables as $index => $table) {
                $tableList .= "\nTable ".($index + 1).":\n";
                if (! empty($table['headers'])) {
                    $tableList .= 'Headers: '.implode(' | ', $table['headers'])."\n";
                }
                $tableList .= "Rows:\n";
                foreach ($table['rows'] as $row) {
                    $tableList .= '  - '.implode(' | ', $row)."\n";
                }
            }
        }

        $layoutInfo = '';
        if (! empty($layoutSections)) {
            $layoutInfo = "\n\n=== DETECTED LAYOUT STRUCTURE (MUST REPLICATE EXACTLY) ===\n";
            $layoutInfo .= "The following layout sections were detected in the original page. You MUST recreate this exact structure:\n\n";

            foreach ($layoutSections as $index => $section) {
                $layoutInfo .= 'SECTION '.($index + 1).": {$section['type']}\n";

                if ($section['type'] === 'hero') {
                    $layoutInfo .= "  → Use a 'hero' block with:\n";
                    if (! empty($section['content']['heading'])) {
                        $layoutInfo .= "    - heading: \"{$section['content']['heading']}\"\n";
                    }
                    if (! empty($section['content']['subheading'])) {
                        $layoutInfo .= "    - subheading: \"{$section['content']['subheading']}\"\n";
                    }
                    if (! empty($section['content']['image'])) {
                        $layoutInfo .= "    - image: \"{$section['content']['image']}\"\n";
                    }
                    if (! empty($section['content']['cta_text'])) {
                        $layoutInfo .= "    - cta_text: \"{$section['content']['cta_text']}\"\n";
                        $layoutInfo .= "    - cta_url: \"{$section['content']['cta_url']}\"\n";
                    }
                } elseif ($section['type'] === 'grid') {
                    $cols = $section['columns'] ?? 3;
                    $itemCount = count($section['items'] ?? []);
                    $layoutInfo .= "  → Use a 'grid' block with columns: {$cols}\n";
                    $layoutInfo .= "  → Contains {$itemCount} child items arranged side-by-side:\n";

                    foreach ($section['items'] ?? [] as $i => $item) {
                        $layoutInfo .= '    Child '.($i + 1).': ';
                        if (! empty($item['image']) && ! empty($item['title'])) {
                            $layoutInfo .= "card with image\n";
                            $layoutInfo .= "      - title: \"{$item['title']}\"\n";
                            $layoutInfo .= "      - image: \"{$item['image']}\"\n";
                            if (! empty($item['description'])) {
                                $layoutInfo .= '      - content: "'.mb_substr($item['description'], 0, 100)."...\"\n";
                            }
                        } elseif (! empty($item['title'])) {
                            $layoutInfo .= "card\n";
                            $layoutInfo .= "      - title: \"{$item['title']}\"\n";
                            if (! empty($item['description'])) {
                                $layoutInfo .= '      - content: "'.mb_substr($item['description'], 0, 100)."...\"\n";
                            }
                        } elseif (! empty($item['image'])) {
                            $layoutInfo .= "image\n";
                            $layoutInfo .= "      - src: \"{$item['image']}\"\n";
                        }
                    }
                } elseif ($section['type'] === 'columns') {
                    $layout = $section['layout'] ?? '1/2-1/2';
                    $layoutInfo .= "  → Use a 'columns' block with layout: {$layout}\n";
                    $layoutInfo .= "  → Contains 2 side-by-side items:\n";

                    foreach ($section['items'] ?? [] as $i => $item) {
                        $layoutInfo .= '    '.($i === 0 ? 'Left' : 'Right').' column: ';
                        if ($item['content_type'] === 'image') {
                            $layoutInfo .= "image block\n";
                            $layoutInfo .= "      - src: \"{$item['image']}\"\n";
                        } else {
                            $layoutInfo .= "text content (paragraph or rich_text)\n";
                            if (! empty($item['content'])) {
                                $layoutInfo .= '      - text: "'.mb_substr($item['content'], 0, 100)."...\"\n";
                            }
                        }
                    }
                } elseif ($section['type'] === 'cta') {
                    $layoutInfo .= "  → Use a 'cta' block with:\n";
                    if (! empty($section['content']['heading'])) {
                        $layoutInfo .= "    - heading: \"{$section['content']['heading']}\"\n";
                    }
                    if (! empty($section['content']['description'])) {
                        $layoutInfo .= "    - content: \"{$section['content']['description']}\"\n";
                    }
                    if (! empty($section['content']['button_text'])) {
                        $layoutInfo .= "    - button_text: \"{$section['content']['button_text']}\"\n";
                        $layoutInfo .= "    - button_url: \"{$section['content']['button_url']}\"\n";
                    }
                } elseif ($section['type'] === 'faq') {
                    $itemCount = count($section['items'] ?? []);
                    $layoutInfo .= "  → Use a 'faq' block with {$itemCount} Q&A items:\n";
                    foreach ($section['items'] ?? [] as $i => $item) {
                        $layoutInfo .= '    '.($i + 1).". Q: \"{$item['question']}\"\n";
                        $layoutInfo .= '       A: "'.mb_substr($item['answer'], 0, 80)."...\"\n";
                    }
                }

                $layoutInfo .= "\n";
            }

            $layoutInfo .= "=== END LAYOUT STRUCTURE ===\n";
        }

        return <<<PROMPT
You are a web page layout replication expert. Your task is to EXACTLY RECREATE the layout and structure of the source page using the available block types.

**CRITICAL: The goal is to make the imported page look IDENTICAL to the original. Preserve the exact layout, structure, and visual arrangement.**

Source URL: {$url}
Page Title: {$title}

{$blockTypes}
{$imageList}
{$tableList}
{$layoutInfo}

CONTENT TO ANALYZE:
---
{$content}
---

LAYOUT REPLICATION RULES (FOLLOW STRICTLY):

1. **PRESERVE EXACT LAYOUT STRUCTURE**
   - If the original has items side-by-side, use a grid or columns block
   - If there are 3 cards in a row, use grid with columns: 3
   - If there's image + text side by side, use columns with layout "1/2-1/2"
   - NEVER flatten a multi-column layout into single-column unless the original is single-column

2. **USE THE DETECTED LAYOUT SECTIONS**
   - The DETECTED LAYOUT STRUCTURE section above shows exactly what was found
   - Recreate each section using the specified block type
   - Use the exact content, images, and structure described

3. **GRID BLOCKS FOR MULTI-COLUMN CONTENT**
   - Multiple cards/items in a row → grid block with children
   - Feature sections with 3-4 items → grid with columns matching item count
   - Gallery or product listings → grid block
   - Each grid child should have column_span: 1 (takes one grid column)

4. **COLUMNS BLOCKS FOR TWO-COLUMN LAYOUTS**
   - Image on left, text on right → columns with "1/2-1/2"
   - Sidebar layouts → columns with "1/3-2/3" or "2/3-1/3"

5. **HERO SECTIONS**
   - Full-width banner with heading → hero block with image if available
   - Include CTA button if present

6. **INCLUDE ALL IMAGES**
   - Use exact image URLs from the AVAILABLE IMAGES list
   - Place images in their original positions (in hero, in cards, standalone)
   - If image appears in a card in the original, put it in the card's image field

7. **INCLUDE ALL TABLES**
   - Use exact headers and rows from TABLES EXTRACTED section
   - Place tables in their original position in the page flow

8. **NESTING IS REQUIRED**
   - Grid and columns blocks MUST have children array
   - Children are the blocks that appear inside the grid/columns
   - Example: grid with 3 cards = grid block with 3 card children

OUTPUT FORMAT:
Return ONLY valid JSON. Each block needs:
- "type": block type name
- "content": object matching the block type
- "column_span": 12 for full-width, or smaller for narrower
- "children": (for grid/columns only) array of nested blocks

EXAMPLE - Page with hero and 3 feature cards:
```json
[
  {
    "type": "hero",
    "content": {"heading": "Welcome", "subheading": "Description", "image": "https://example.com/hero.jpg", "cta_text": "Learn More", "cta_url": "/about"},
    "column_span": 12
  },
  {"type": "heading", "content": {"text": "Our Features", "level": 2}, "column_span": 12},
  {
    "type": "grid",
    "content": {"columns": 3, "gap": 4},
    "column_span": 12,
    "children": [
      {"type": "card", "content": {"title": "Feature 1", "content": "Description 1", "image": "https://example.com/f1.jpg"}, "column_span": 1},
      {"type": "card", "content": {"title": "Feature 2", "content": "Description 2", "image": "https://example.com/f2.jpg"}, "column_span": 1},
      {"type": "card", "content": {"title": "Feature 3", "content": "Description 3", "image": "https://example.com/f3.jpg"}, "column_span": 1}
    ]
  }
]
```

EXAMPLE - Two-column image + text:
```json
[
  {
    "type": "columns",
    "content": {"layout": "1/2-1/2"},
    "column_span": 12,
    "children": [
      {"type": "image", "content": {"src": "https://example.com/photo.jpg", "alt": "Photo", "caption": ""}, "column_span": 1},
      {"type": "rich_text", "content": {"html": "<p>Description text here...</p>"}, "column_span": 1}
    ]
  }
]
```

Return ONLY the JSON array, no additional text.
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

            $validBlock = [
                'type' => $type,
                'content' => array_merge(
                    $blockType->defaultContent(),
                    $block['content'] ?? []
                ),
                'settings' => $blockType->defaultSettings(),
                'column_span' => min(12, max(1, (int) ($block['column_span'] ?? 12))),
            ];

            // Handle nested children for container blocks (grid, columns)
            if ($blockType->supportsChildren() && ! empty($block['children']) && is_array($block['children'])) {
                $validBlock['children'] = $this->validateBlocks($block['children']);
            }

            $validBlocks[] = $validBlock;
        }

        return $validBlocks;
    }
}

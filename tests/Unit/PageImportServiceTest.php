<?php

use App\Services\PageImportService;

describe('PageImportService', function () {
    it('validates simple blocks correctly', function () {
        $service = new PageImportService;

        $blocks = [
            ['type' => 'heading', 'content' => ['text' => 'Hello', 'level' => 1], 'column_span' => 12],
            ['type' => 'paragraph', 'content' => ['text' => 'World'], 'column_span' => 12],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result)->toHaveCount(2);
        expect($result[0]['type'])->toBe('heading');
        expect($result[0]['content']['text'])->toBe('Hello');
        expect($result[1]['type'])->toBe('paragraph');
    });

    it('validates grid blocks with nested children', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'grid',
                'content' => ['columns' => 3, 'gap' => 4],
                'column_span' => 12,
                'children' => [
                    ['type' => 'card', 'content' => ['title' => 'Card 1', 'content' => 'Desc 1'], 'column_span' => 1],
                    ['type' => 'card', 'content' => ['title' => 'Card 2', 'content' => 'Desc 2'], 'column_span' => 1],
                    ['type' => 'card', 'content' => ['title' => 'Card 3', 'content' => 'Desc 3'], 'column_span' => 1],
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result)->toHaveCount(1);
        expect($result[0]['type'])->toBe('grid');
        expect($result[0]['content']['columns'])->toBe(3);
        expect($result[0]['content']['gap'])->toBe(4);
        expect($result[0]['children'])->toHaveCount(3);
        expect($result[0]['children'][0]['type'])->toBe('card');
        expect($result[0]['children'][0]['content']['title'])->toBe('Card 1');
        expect($result[0]['children'][1]['content']['title'])->toBe('Card 2');
        expect($result[0]['children'][2]['content']['title'])->toBe('Card 3');
    });

    it('validates columns blocks with nested children', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'columns',
                'content' => ['layout' => '1/2-1/2'],
                'column_span' => 12,
                'children' => [
                    ['type' => 'image', 'content' => ['src' => 'https://example.com/img.jpg', 'alt' => 'Image'], 'column_span' => 1],
                    ['type' => 'paragraph', 'content' => ['text' => 'Description'], 'column_span' => 1],
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result)->toHaveCount(1);
        expect($result[0]['type'])->toBe('columns');
        expect($result[0]['content']['layout'])->toBe('1/2-1/2');
        expect($result[0]['children'])->toHaveCount(2);
        expect($result[0]['children'][0]['type'])->toBe('image');
        expect($result[0]['children'][1]['type'])->toBe('paragraph');
    });

    it('validates deeply nested grids', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'grid',
                'content' => ['columns' => 2, 'gap' => 4],
                'column_span' => 12,
                'children' => [
                    [
                        'type' => 'grid',
                        'content' => ['columns' => 2, 'gap' => 2],
                        'column_span' => 1,
                        'children' => [
                            ['type' => 'card', 'content' => ['title' => 'Nested 1'], 'column_span' => 1],
                            ['type' => 'card', 'content' => ['title' => 'Nested 2'], 'column_span' => 1],
                        ],
                    ],
                    ['type' => 'paragraph', 'content' => ['text' => 'Right side'], 'column_span' => 1],
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result)->toHaveCount(1);
        expect($result[0]['children'])->toHaveCount(2);
        expect($result[0]['children'][0]['type'])->toBe('grid');
        expect($result[0]['children'][0]['children'])->toHaveCount(2);
        expect($result[0]['children'][0]['children'][0]['content']['title'])->toBe('Nested 1');
    });

    it('filters out invalid child block types', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'grid',
                'content' => ['columns' => 2, 'gap' => 4],
                'column_span' => 12,
                'children' => [
                    ['type' => 'card', 'content' => ['title' => 'Valid'], 'column_span' => 1],
                    ['type' => 'invalid_type', 'content' => ['foo' => 'bar'], 'column_span' => 1],
                    ['type' => 'paragraph', 'content' => ['text' => 'Also valid'], 'column_span' => 1],
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result[0]['children'])->toHaveCount(2);
        expect($result[0]['children'][0]['type'])->toBe('card');
        expect($result[0]['children'][1]['type'])->toBe('paragraph');
    });

    it('does not add children to non-container blocks', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'paragraph',
                'content' => ['text' => 'I am a paragraph'],
                'column_span' => 12,
                'children' => [
                    ['type' => 'card', 'content' => ['title' => 'Should be ignored'], 'column_span' => 1],
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        expect($result)->toHaveCount(1);
        expect($result[0]['type'])->toBe('paragraph');
        expect($result[0])->not->toHaveKey('children');
    });

    it('parses AI response with nested grid blocks', function () {
        $service = new PageImportService;

        $aiResponse = <<<'JSON'
```json
[
  {"type": "heading", "content": {"text": "Our Features", "level": 2}, "column_span": 12},
  {
    "type": "grid",
    "content": {"columns": 3, "gap": 4},
    "column_span": 12,
    "children": [
      {"type": "card", "content": {"title": "Feature 1", "content": "Description 1", "image": ""}, "column_span": 1},
      {"type": "card", "content": {"title": "Feature 2", "content": "Description 2", "image": ""}, "column_span": 1},
      {"type": "card", "content": {"title": "Feature 3", "content": "Description 3", "image": ""}, "column_span": 1}
    ]
  }
]
```
JSON;

        $result = invokePrivateMethod($service, 'parseAiResponse', [$aiResponse]);

        expect($result)->toHaveCount(2);
        expect($result[0]['type'])->toBe('heading');
        expect($result[1]['type'])->toBe('grid');
        expect($result[1]['children'])->toHaveCount(3);
        expect($result[1]['children'][0]['content']['title'])->toBe('Feature 1');
    });

    it('merges default content for child blocks', function () {
        $service = new PageImportService;

        $blocks = [
            [
                'type' => 'grid',
                'content' => ['columns' => 2],  // Missing 'gap', should use default
                'column_span' => 12,
                'children' => [
                    ['type' => 'card', 'content' => ['title' => 'Card'], 'column_span' => 1],  // Missing other card fields
                ],
            ],
        ];

        $result = invokePrivateMethod($service, 'validateBlocks', [$blocks]);

        // Grid should have default gap
        expect($result[0]['content']['gap'])->toBe(4);

        // Card should have default content and image fields
        expect($result[0]['children'][0]['content'])->toHaveKey('content');
        expect($result[0]['children'][0]['content'])->toHaveKey('image');
    });

    it('extracts hero section from HTML', function () {
        $service = new PageImportService;

        $html = <<<'HTML'
<html>
<body>
<section class="hero-section">
    <h1>Welcome to Our Site</h1>
    <p>We provide excellent services</p>
    <a href="/contact">Get Started</a>
</section>
</body>
</html>
HTML;

        $result = invokePrivateMethod($service, 'extractLayoutSections', [$html, 'https://example.com']);

        expect($result)->toHaveCount(1);
        expect($result[0]['type'])->toBe('hero');
        expect($result[0]['content']['heading'])->toBe('Welcome to Our Site');
        expect($result[0]['content']['subheading'])->toBe('We provide excellent services');
        expect($result[0]['content']['cta_text'])->toBe('Get Started');
        expect($result[0]['content']['cta_url'])->toBe('https://example.com/contact');
    });

    it('extracts grid layout with cards from HTML', function () {
        $service = new PageImportService;

        $html = <<<'HTML'
<html>
<body>
<div class="features">
    <div class="feature-item">
        <img src="/img/feature1.jpg" alt="Feature 1">
        <h3>Feature One</h3>
        <p>Description of feature one</p>
    </div>
    <div class="feature-item">
        <img src="/img/feature2.jpg" alt="Feature 2">
        <h3>Feature Two</h3>
        <p>Description of feature two</p>
    </div>
    <div class="feature-item">
        <img src="/img/feature3.jpg" alt="Feature 3">
        <h3>Feature Three</h3>
        <p>Description of feature three</p>
    </div>
</div>
</body>
</html>
HTML;

        $result = invokePrivateMethod($service, 'extractLayoutSections', [$html, 'https://example.com']);

        $gridSection = collect($result)->firstWhere('type', 'grid');
        expect($gridSection)->not->toBeNull();
        expect($gridSection['columns'])->toBe(3);
        expect($gridSection['items'])->toHaveCount(3);
        expect($gridSection['items'][0]['title'])->toBe('Feature One');
        expect($gridSection['items'][0]['image'])->toBe('https://example.com/img/feature1.jpg');
        expect($gridSection['items'][1]['title'])->toBe('Feature Two');
        expect($gridSection['items'][2]['title'])->toBe('Feature Three');
    });

    it('extracts FAQ section from HTML', function () {
        $service = new PageImportService;

        $html = <<<'HTML'
<html>
<body>
<section class="faq-section">
    <dt>What is your return policy?</dt>
    <dd>You can return items within 30 days.</dd>
    <dt>How long does shipping take?</dt>
    <dd>Shipping typically takes 3-5 business days.</dd>
</section>
</body>
</html>
HTML;

        $result = invokePrivateMethod($service, 'extractLayoutSections', [$html, 'https://example.com']);

        $faqSection = collect($result)->firstWhere('type', 'faq');
        expect($faqSection)->not->toBeNull();
        expect($faqSection['items'])->toHaveCount(2);
        expect($faqSection['items'][0]['question'])->toBe('What is your return policy?');
        expect($faqSection['items'][0]['answer'])->toBe('You can return items within 30 days.');
        expect($faqSection['items'][1]['question'])->toBe('How long does shipping take?');
    });

    it('extracts CTA section from HTML', function () {
        $service = new PageImportService;

        $html = <<<'HTML'
<html>
<body>
<section class="cta-section">
    <h2>Ready to get started?</h2>
    <p>Join thousands of happy customers today.</p>
    <a href="/signup" class="btn">Sign Up Now</a>
</section>
</body>
</html>
HTML;

        $result = invokePrivateMethod($service, 'extractLayoutSections', [$html, 'https://example.com']);

        $ctaSection = collect($result)->firstWhere('type', 'cta');
        expect($ctaSection)->not->toBeNull();
        expect($ctaSection['content']['heading'])->toBe('Ready to get started?');
        expect($ctaSection['content']['description'])->toBe('Join thousands of happy customers today.');
        expect($ctaSection['content']['button_text'])->toBe('Sign Up Now');
        expect($ctaSection['content']['button_url'])->toBe('https://example.com/signup');
    });

    it('resolves relative URLs to absolute in layout sections', function () {
        $service = new PageImportService;

        $html = <<<'HTML'
<html>
<body>
<section class="hero-banner">
    <img src="/images/hero-bg.jpg">
    <h1>Hero Title</h1>
    <a href="contact.html">Contact Us</a>
</section>
</body>
</html>
HTML;

        $result = invokePrivateMethod($service, 'extractLayoutSections', [$html, 'https://example.com']);

        expect($result[0]['content']['image'])->toBe('https://example.com/images/hero-bg.jpg');
        expect($result[0]['content']['cta_url'])->toBe('https://example.com/contact.html');
    });
});

/**
 * Helper function to invoke private methods for testing.
 */
function invokePrivateMethod(object $object, string $methodName, array $parameters = []): mixed
{
    $reflection = new ReflectionClass($object);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}

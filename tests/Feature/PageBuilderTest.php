<?php

use App\Enums\BlockType;
use App\Enums\PageEditorRole;
use App\Enums\PageStatus;
use App\Livewire\Pages\Builder;
use App\Livewire\Pages\Create;
use App\Livewire\Pages\Index;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageTemplate;
use App\Models\User;
use Livewire\Livewire;

// Authorization tests
describe('authorization', function () {
    it('denies access to pages index for regular users', function () {
        $user = User::factory()->create(['page_role' => null, 'is_admin' => false]);

        $this->actingAs($user)
            ->get(route('pages.index'))
            ->assertForbidden();
    });

    it('allows page editors to access pages index', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        $this->actingAs($user)
            ->get(route('pages.index'))
            ->assertOk();
    });

    it('allows page admins to access pages index', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Admin]);

        $this->actingAs($user)
            ->get(route('pages.index'))
            ->assertOk();
    });

    it('allows system admins to access pages index', function () {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get(route('pages.index'))
            ->assertOk();
    });

    it('allows editors to update their own pages', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();

        $this->actingAs($user)
            ->get(route('pages.builder', $page))
            ->assertOk();
    });

    it('denies editors from updating other users pages', function () {
        $editor = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $otherUser = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($otherUser)->create();

        $this->actingAs($editor)
            ->get(route('pages.builder', $page))
            ->assertForbidden();
    });

    it('allows page admins to update any page', function () {
        $admin = User::factory()->create(['page_role' => PageEditorRole::Admin]);
        $otherUser = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($otherUser)->create();

        $this->actingAs($admin)
            ->get(route('pages.builder', $page))
            ->assertOk();
    });
});

// Page creation tests
describe('page creation', function () {
    it('can create a new page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Test Page')
            ->set('slug', 'test-page')
            ->set('layout', 'default')
            ->call('create')
            ->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'status' => PageStatus::Draft->value,
            'created_by' => $user->id,
        ]);
    });

    it('creates a page from a template', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $template = PageTemplate::factory()->create([
            'structure' => [
                ['type' => BlockType::Heading->value, 'content' => ['text' => 'Hello', 'level' => 1]],
                ['type' => BlockType::Paragraph->value, 'content' => ['text' => 'World']],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Template Page')
            ->set('slug', 'template-page')
            ->set('layout', 'default')
            ->set('templateId', $template->id)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'template-page')->first();
        expect($page->allBlocks)->toHaveCount(2);
        expect($page->allBlocks->first()->type)->toBe(BlockType::Heading);
    });

    it('creates a page from imported blocks', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        $importedBlocks = [
            ['type' => 'heading', 'content' => ['text' => 'Imported Title', 'level' => 1], 'column_span' => 12],
            ['type' => 'paragraph', 'content' => ['text' => 'Imported content'], 'column_span' => 12],
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Imported Page')
            ->set('slug', 'imported-page')
            ->set('layout', 'default')
            ->set('importedBlocks', $importedBlocks)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'imported-page')->first();
        expect($page->allBlocks)->toHaveCount(2);
        expect($page->allBlocks->first()->type)->toBe(BlockType::Heading);
        expect($page->allBlocks->first()->getContent('text'))->toBe('Imported Title');
    });

    it('prioritizes imported blocks over template', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $template = PageTemplate::factory()->create([
            'structure' => [
                ['type' => BlockType::Hero->value, 'content' => BlockType::Hero->defaultContent()],
            ],
        ]);

        $importedBlocks = [
            ['type' => 'heading', 'content' => ['text' => 'Imported', 'level' => 1], 'column_span' => 12],
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Priority Test')
            ->set('slug', 'priority-test')
            ->set('layout', 'default')
            ->set('templateId', $template->id)
            ->set('importedBlocks', $importedBlocks)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'priority-test')->first();
        expect($page->allBlocks)->toHaveCount(1);
        expect($page->allBlocks->first()->type)->toBe(BlockType::Heading);
    });

    it('creates a page with imported grid and nested children', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        $importedBlocks = [
            ['type' => 'heading', 'content' => ['text' => 'Features', 'level' => 2], 'column_span' => 12],
            [
                'type' => 'grid',
                'content' => ['columns' => 3, 'gap' => 4],
                'column_span' => 12,
                'children' => [
                    ['type' => 'card', 'content' => ['title' => 'Card 1', 'content' => 'Description 1', 'image' => ''], 'column_span' => 1],
                    ['type' => 'card', 'content' => ['title' => 'Card 2', 'content' => 'Description 2', 'image' => ''], 'column_span' => 1],
                    ['type' => 'card', 'content' => ['title' => 'Card 3', 'content' => 'Description 3', 'image' => ''], 'column_span' => 1],
                ],
            ],
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Grid Page')
            ->set('slug', 'grid-page')
            ->set('layout', 'default')
            ->set('importedBlocks', $importedBlocks)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'grid-page')->first();

        // Should have heading + grid + 3 children = 5 total blocks
        expect($page->allBlocks)->toHaveCount(5);

        // Check top-level blocks (heading and grid)
        $topLevelBlocks = $page->blocks()->whereNull('parent_id')->orderBy('order')->get();
        expect($topLevelBlocks)->toHaveCount(2);
        expect($topLevelBlocks[0]->type)->toBe(BlockType::Heading);
        expect($topLevelBlocks[1]->type)->toBe(BlockType::Grid);

        // Check grid content
        $gridBlock = $topLevelBlocks[1];
        expect($gridBlock->getContent('columns'))->toBe(3);
        expect($gridBlock->getContent('gap'))->toBe(4);

        // Check children
        expect($gridBlock->children)->toHaveCount(3);
        expect($gridBlock->children[0]->type)->toBe(BlockType::Card);
        expect($gridBlock->children[0]->getContent('title'))->toBe('Card 1');
        expect($gridBlock->children[1]->getContent('title'))->toBe('Card 2');
        expect($gridBlock->children[2]->getContent('title'))->toBe('Card 3');
    });

    it('creates deeply nested grids with correct parent relationships', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        $importedBlocks = [
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
                            ['type' => 'card', 'content' => ['title' => 'Nested Card 1', 'content' => '', 'image' => ''], 'column_span' => 1],
                            ['type' => 'card', 'content' => ['title' => 'Nested Card 2', 'content' => '', 'image' => ''], 'column_span' => 1],
                        ],
                    ],
                    ['type' => 'paragraph', 'content' => ['text' => 'Right side text'], 'column_span' => 1],
                ],
            ],
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Nested Grid Page')
            ->set('slug', 'nested-grid-page')
            ->set('layout', 'default')
            ->set('importedBlocks', $importedBlocks)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'nested-grid-page')->first();

        // Should have: outer grid + inner grid + paragraph + 2 nested cards = 5 blocks
        expect($page->allBlocks)->toHaveCount(5);

        // Top-level: just the outer grid
        $topLevel = $page->blocks()->whereNull('parent_id')->get();
        expect($topLevel)->toHaveCount(1);
        expect($topLevel[0]->type)->toBe(BlockType::Grid);

        $outerGrid = $topLevel[0];
        expect($outerGrid->children)->toHaveCount(2);

        // First child is inner grid
        $innerGrid = $outerGrid->children[0];
        expect($innerGrid->type)->toBe(BlockType::Grid);
        expect($innerGrid->parent_id)->toBe($outerGrid->id);
        expect($innerGrid->children)->toHaveCount(2);

        // Inner grid's children
        expect($innerGrid->children[0]->type)->toBe(BlockType::Card);
        expect($innerGrid->children[0]->parent_id)->toBe($innerGrid->id);
        expect($innerGrid->children[0]->getContent('title'))->toBe('Nested Card 1');

        // Second child of outer grid is paragraph
        $paragraph = $outerGrid->children[1];
        expect($paragraph->type)->toBe(BlockType::Paragraph);
        expect($paragraph->parent_id)->toBe($outerGrid->id);
    });

    it('creates columns layout with image and text side by side', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);

        $importedBlocks = [
            ['type' => 'heading', 'content' => ['text' => 'About Us', 'level' => 2], 'column_span' => 12],
            [
                'type' => 'columns',
                'content' => ['layout' => '1/2-1/2'],
                'column_span' => 12,
                'children' => [
                    ['type' => 'image', 'content' => ['src' => 'https://example.com/team.jpg', 'alt' => 'Our team', 'caption' => ''], 'column_span' => 1],
                    ['type' => 'rich_text', 'content' => ['html' => '<p>We are a <strong>great</strong> team.</p>'], 'column_span' => 1],
                ],
            ],
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Columns Page')
            ->set('slug', 'columns-page')
            ->set('layout', 'default')
            ->set('importedBlocks', $importedBlocks)
            ->call('create')
            ->assertRedirect();

        $page = Page::where('slug', 'columns-page')->first();

        // heading + columns + 2 children = 4 blocks
        expect($page->allBlocks)->toHaveCount(4);

        $topLevel = $page->blocks()->whereNull('parent_id')->orderBy('order')->get();
        expect($topLevel)->toHaveCount(2);
        expect($topLevel[0]->type)->toBe(BlockType::Heading);
        expect($topLevel[1]->type)->toBe(BlockType::Columns);

        $columnsBlock = $topLevel[1];
        expect($columnsBlock->getContent('layout'))->toBe('1/2-1/2');
        expect($columnsBlock->children)->toHaveCount(2);
        expect($columnsBlock->children[0]->type)->toBe(BlockType::Image);
        expect($columnsBlock->children[0]->getContent('src'))->toBe('https://example.com/team.jpg');
        expect($columnsBlock->children[1]->type)->toBe(BlockType::RichText);
    });

    it('validates unique slug', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        Page::factory()->create(['slug' => 'existing-page']);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Test Page')
            ->set('slug', 'existing-page')
            ->set('layout', 'default')
            ->call('create')
            ->assertHasErrors(['slug']);
    });
});

// Page builder tests
describe('page builder', function () {
    it('can add a block to a page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('addBlock', 'heading')
            ->assertSet('selectedBlockId', fn ($id) => $id !== null);

        expect($page->refresh()->allBlocks)->toHaveCount(1);
        expect($page->allBlocks->first()->type)->toBe(BlockType::Heading);
    });

    it('can delete a block', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading()->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('deleteBlock', $block->id);

        expect($page->refresh()->allBlocks)->toHaveCount(0);
    });

    it('can duplicate a block', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading('Test')->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('duplicateBlock', $block->id);

        expect($page->refresh()->allBlocks)->toHaveCount(2);
    });

    it('can update block content', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading()->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('selectBlock', $block->id)
            ->set('editingContent.text', 'Updated Heading')
            ->call('updateBlockContent', $block->id);

        expect($block->refresh()->getContent('text'))->toBe('Updated Heading');
    });

    it('can reorder blocks via handleSort', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block1 = PageBlock::factory()->heading()->create(['page_id' => $page->id, 'order' => 0]);
        $block2 = PageBlock::factory()->paragraph()->create(['page_id' => $page->id, 'order' => 1]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('handleSort', $block2->id, 0);

        expect($block2->refresh()->order)->toBe(0);
        expect($block1->refresh()->order)->toBe(1);
    });

    it('can move a block to become a child of a grid', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();

        // Create a grid and a standalone paragraph
        $grid = PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => BlockType::Grid,
            'content' => ['columns' => 3, 'gap' => 4],
            'order' => 0,
        ]);
        $paragraph = PageBlock::factory()->paragraph()->create([
            'page_id' => $page->id,
            'order' => 1,
        ]);

        // Move paragraph into grid
        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('moveBlockToParent', $paragraph->id, $grid->id);

        $paragraph->refresh();
        expect($paragraph->parent_id)->toBe($grid->id);
        expect($paragraph->column_span)->toBe(1);

        // Grid should now have 1 child
        $grid->refresh();
        expect($grid->children)->toHaveCount(1);
    });

    it('prevents moving a block into a non-container block', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();

        $heading = PageBlock::factory()->heading()->create([
            'page_id' => $page->id,
            'order' => 0,
        ]);
        $paragraph = PageBlock::factory()->paragraph()->create([
            'page_id' => $page->id,
            'order' => 1,
        ]);

        // Try to move paragraph into heading (which doesn't support children)
        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('moveBlockToParent', $paragraph->id, $heading->id);

        // Paragraph should remain top-level
        $paragraph->refresh();
        expect($paragraph->parent_id)->toBeNull();
    });

    it('prevents moving a container into itself', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();

        $grid = PageBlock::factory()->create([
            'page_id' => $page->id,
            'type' => BlockType::Grid,
            'content' => ['columns' => 2, 'gap' => 4],
            'order' => 0,
        ]);

        // Try to move grid into itself
        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('moveBlockToParent', $grid->id, $grid->id);

        // Grid should remain top-level
        $grid->refresh();
        expect($grid->parent_id)->toBeNull();
    });

    it('can publish a page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create(['status' => PageStatus::Draft]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('publishPage');

        $page->refresh();
        expect($page->status)->toBe(PageStatus::Published);
        expect($page->published_at)->not->toBeNull();
    });

    it('can unpublish a page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->published()->create();

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('unpublishPage');

        expect($page->refresh()->status)->toBe(PageStatus::Draft);
    });

    it('can apply a template to existing page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        PageBlock::factory()->heading()->create(['page_id' => $page->id]);

        $template = PageTemplate::factory()->create([
            'structure' => [
                ['type' => BlockType::Hero->value, 'content' => BlockType::Hero->defaultContent()],
            ],
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('applyTemplate', $template->id);

        $page->refresh();
        expect($page->allBlocks)->toHaveCount(1);
        expect($page->allBlocks->first()->type)->toBe(BlockType::Hero);
    });

    it('can add a block at a specific position', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block1 = PageBlock::factory()->heading()->create(['page_id' => $page->id, 'order' => 0]);
        $block2 = PageBlock::factory()->paragraph()->create(['page_id' => $page->id, 'order' => 1]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('addBlockAtPosition', 'image', 1);

        $page->refresh();
        expect($page->blocks)->toHaveCount(3);

        $newBlock = $page->blocks->where('type', BlockType::Image)->first();
        expect($newBlock->order)->toBe(1);
        expect($block1->refresh()->order)->toBe(0);
        expect($block2->refresh()->order)->toBe(2);
    });

    it('can add a block to a parent container', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $grid = PageBlock::factory()->ofType(BlockType::Grid)->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('addBlockToParent', 'heading', $grid->id);

        $page->refresh();
        expect($grid->children)->toHaveCount(1);
        expect($grid->children->first()->type)->toBe(BlockType::Heading);
        expect($grid->children->first()->parent_id)->toBe($grid->id);
    });

    it('adds block at position 0 when dropping before first block', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $existingBlock = PageBlock::factory()->heading()->create(['page_id' => $page->id, 'order' => 0]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('addBlockAtPosition', 'paragraph', 0);

        $page->refresh();
        $newBlock = $page->blocks->where('type', BlockType::Paragraph)->first();
        expect($newBlock->order)->toBe(0);
        expect($existingBlock->refresh()->order)->toBe(1);
    });

    it('can update block text inline', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading('Original')->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('updateInlineText', $block->id, 'text', 'Updated Inline');

        expect($block->refresh()->getContent('text'))->toBe('Updated Inline');
    });

    it('syncs inline edits with sidebar when block is selected', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading('Original')->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('selectBlock', $block->id)
            ->assertSet('editingContent.text', 'Original')
            ->call('updateInlineText', $block->id, 'text', 'Updated Inline')
            ->assertSet('editingContent.text', 'Updated Inline');
    });

    it('can update rich text html inline', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->ofType(BlockType::RichText)->create([
            'page_id' => $page->id,
            'content' => ['html' => '<p>Original</p>'],
        ]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('updateInlineText', $block->id, 'html', '<p>Updated <strong>Inline</strong></p>');

        expect($block->refresh()->getContent('html'))->toBe('<p>Updated <strong>Inline</strong></p>');
    });
});

// Page index tests
describe('page index', function () {
    it('displays pages in the index', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create(['title' => 'My Test Page']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee('My Test Page');
    });

    it('can search pages by title', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Admin]);
        Page::factory()->create(['title' => 'Alpha Page']);
        Page::factory()->create(['title' => 'Beta Page']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Alpha')
            ->assertSee('Alpha Page')
            ->assertDontSee('Beta Page');
    });

    it('can filter pages by status', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Admin]);
        Page::factory()->create(['title' => 'Draft Page', 'status' => PageStatus::Draft]);
        Page::factory()->published()->create(['title' => 'Published Page']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('status', 'published')
            ->assertDontSee('Draft Page')
            ->assertSee('Published Page');
    });

    it('can delete a page from index', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create(['title' => 'Delete Me']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('deletePage', $page);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    });

    it('can duplicate a page', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create(['title' => 'Original']);
        PageBlock::factory()->heading('Header')->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('duplicatePage', $page)
            ->assertRedirect();

        expect(Page::count())->toBe(2);
        $copy = Page::where('title', 'Original (Copy)')->first();
        expect($copy)->not->toBeNull();
        expect($copy->allBlocks)->toHaveCount(1);
    });
});

// Public page display tests
describe('public page display', function () {
    it('displays a published page', function () {
        $page = Page::factory()->published()->create([
            'title' => 'Public Page',
            'slug' => 'public-test',
        ]);
        PageBlock::factory()->heading('Welcome')->create(['page_id' => $page->id]);

        $this->get(route('pages.show', 'public-test'))
            ->assertOk()
            ->assertSee('Welcome');
    });

    it('returns 404 for draft pages', function () {
        $page = Page::factory()->create([
            'slug' => 'draft-test',
            'status' => PageStatus::Draft,
        ]);

        $this->get(route('pages.show', 'draft-test'))
            ->assertNotFound();
    });

    it('returns 404 for archived pages', function () {
        $page = Page::factory()->archived()->create([
            'slug' => 'archived-test',
        ]);

        $this->get(route('pages.show', 'archived-test'))
            ->assertNotFound();
    });

    it('returns 404 for non-existent slugs', function () {
        $this->get(route('pages.show', 'non-existent'))
            ->assertNotFound();
    });
});

// Model tests
describe('models', function () {
    it('creates a page with uuid', function () {
        $page = Page::factory()->create();

        expect($page->id)->toBeString();
        expect(strlen($page->id))->toBe(36);
    });

    it('page has blocks relationship', function () {
        $page = Page::factory()->create();
        PageBlock::factory()->count(3)->create(['page_id' => $page->id]);

        expect($page->blocks)->toHaveCount(3);
    });

    it('block has parent-child relationship', function () {
        $page = Page::factory()->create();
        $parent = PageBlock::factory()->ofType(BlockType::Grid)->create(['page_id' => $page->id]);
        $child = PageBlock::factory()->heading()->create([
            'page_id' => $page->id,
            'parent_id' => $parent->id,
        ]);

        expect($parent->children)->toHaveCount(1);
        expect($child->parent->id)->toBe($parent->id);
    });

    it('page template has structure', function () {
        $template = PageTemplate::factory()->create([
            'structure' => [
                ['type' => 'heading', 'content' => ['text' => 'Test']],
            ],
        ]);

        expect($template->structure)->toBeArray();
        expect($template->structure[0]['type'])->toBe('heading');
    });
});

// Block settings tests
describe('block settings', function () {
    it('block has default settings including max_width', function () {
        $page = Page::factory()->create();
        $block = PageBlock::factory()->heading()->create(['page_id' => $page->id]);

        expect($block->settings)->toHaveKey('max_width');
        expect($block->getSetting('max_width'))->toBe('full');
    });

    it('can update block max_width setting', function () {
        $user = User::factory()->create(['page_role' => PageEditorRole::Editor]);
        $page = Page::factory()->forUser($user)->create();
        $block = PageBlock::factory()->heading()->create(['page_id' => $page->id]);

        Livewire::actingAs($user)
            ->test(Builder::class, ['page' => $page])
            ->call('selectBlock', $block->id)
            ->set('editingSettings.max_width', 'prose')
            ->assertSet('editingSettings.max_width', 'prose');

        $block->refresh();
        expect($block->getSetting('max_width'))->toBe('prose');
    });

    it('block renderer applies max_width classes', function () {
        $page = Page::factory()->create([
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
        $block = PageBlock::factory()->heading()->create([
            'page_id' => $page->id,
            'content' => ['text' => 'Test Heading', 'level' => 2],
            'settings' => ['max_width' => 'prose', 'padding' => 'none', 'margin' => 'none', 'background' => 'transparent', 'text_align' => 'left'],
        ]);

        $view = view('components.pages.block-renderer', ['block' => $block, 'preview' => false])->render();

        expect($view)->toContain('max-w-prose');
        expect($view)->toContain('mx-auto');
    });

    it('block renderer does not apply mx-auto for full width', function () {
        $page = Page::factory()->create([
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
        $block = PageBlock::factory()->heading()->create([
            'page_id' => $page->id,
            'content' => ['text' => 'Test Heading', 'level' => 2],
            'settings' => ['max_width' => 'full', 'padding' => 'none', 'margin' => 'none', 'background' => 'transparent', 'text_align' => 'left'],
        ]);

        $view = view('components.pages.block-renderer', ['block' => $block, 'preview' => false])->render();

        expect($view)->not->toContain('max-w-');
        expect($view)->not->toContain('mx-auto');
    });
});

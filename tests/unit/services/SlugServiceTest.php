<?php

namespace tests\unit\services;

use app\services\SlugService;

/**
 * Unit tests for SlugService.
 * Requirements: 1.5
 */
class SlugServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var SlugService
     */
    private SlugService $slugService;

    protected function _before(): void
    {
        $this->slugService = new SlugService();
    }

    /**
     * Test basic slug generation from English text.
     */
    public function testCreateSlugFromEnglishText(): void
    {
        $this->assertEquals('hello-world', $this->slugService->createSlug('Hello World'));
        $this->assertEquals('test-article', $this->slugService->createSlug('Test Article'));
        $this->assertEquals('my-first-post', $this->slugService->createSlug('My First Post'));
    }

    /**
     * Test slug generation from Cyrillic text (transliteration).
     */
    public function testCreateSlugFromCyrillicText(): void
    {
        $this->assertEquals('privet-mir', $this->slugService->createSlug('Привет Мир'));
        $this->assertEquals('novaya-statya', $this->slugService->createSlug('Новая статья'));
        $this->assertEquals('testovaya-publikatsiya', $this->slugService->createSlug('Тестовая публикация'));
    }

    /**
     * Test transliteration of all Cyrillic characters.
     */
    public function testTransliterateCyrillicCharacters(): void
    {
        // Test lowercase
        $this->assertEquals('a', $this->slugService->transliterate('а'));
        $this->assertEquals('zh', $this->slugService->transliterate('ж'));
        $this->assertEquals('shch', $this->slugService->transliterate('щ'));
        $this->assertEquals('yu', $this->slugService->transliterate('ю'));
        $this->assertEquals('ya', $this->slugService->transliterate('я'));
        
        // Test uppercase
        $this->assertEquals('A', $this->slugService->transliterate('А'));
        $this->assertEquals('Zh', $this->slugService->transliterate('Ж'));
        $this->assertEquals('Shch', $this->slugService->transliterate('Щ'));
        
        // Test soft/hard signs (should be removed)
        $this->assertEquals('', $this->slugService->transliterate('ь'));
        $this->assertEquals('', $this->slugService->transliterate('ъ'));
    }

    /**
     * Test slug generation with special characters.
     */
    public function testCreateSlugWithSpecialCharacters(): void
    {
        $this->assertEquals('hello-world', $this->slugService->createSlug('Hello, World!'));
        $this->assertEquals('test-123', $this->slugService->createSlug('Test @#$ 123'));
        $this->assertEquals('article-about-php', $this->slugService->createSlug('Article about PHP!!!'));
        $this->assertEquals('what-s-new', $this->slugService->createSlug("What's New?"));
    }

    /**
     * Test slug generation with multiple spaces and hyphens.
     */
    public function testCreateSlugWithMultipleSpaces(): void
    {
        $this->assertEquals('hello-world', $this->slugService->createSlug('Hello    World'));
        $this->assertEquals('test-article', $this->slugService->createSlug('Test - - - Article'));
        $this->assertEquals('my-post', $this->slugService->createSlug('  My   Post  '));
    }

    /**
     * Test slug generation with numbers.
     */
    public function testCreateSlugWithNumbers(): void
    {
        $this->assertEquals('article-2024', $this->slugService->createSlug('Article 2024'));
        $this->assertEquals('top-10-tips', $this->slugService->createSlug('Top 10 Tips'));
        $this->assertEquals('123-test', $this->slugService->createSlug('123 Test'));
    }

    /**
     * Test slug generation with empty or whitespace-only input.
     */
    public function testCreateSlugWithEmptyInput(): void
    {
        $this->assertEquals('', $this->slugService->createSlug(''));
        $this->assertEquals('', $this->slugService->createSlug('   '));
        $this->assertEquals('', $this->slugService->createSlug('!!!'));
    }

    /**
     * Test mixed Cyrillic and Latin text.
     */
    public function testCreateSlugWithMixedText(): void
    {
        $this->assertEquals('hello-mir', $this->slugService->createSlug('Hello Мир'));
        $this->assertEquals('test-statya-2024', $this->slugService->createSlug('Test Статья 2024'));
    }
}

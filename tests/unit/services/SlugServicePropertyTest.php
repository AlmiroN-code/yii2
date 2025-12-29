<?php

namespace tests\unit\services;

use app\services\SlugService;
use Eris\Generators;
use Eris\TestTrait;

/**
 * Property-based tests for SlugService.
 * **Feature: architecture-refactoring, Property 1: Slug generation produces valid slugs**
 * **Validates: Requirements 1.1**
 */
class SlugServicePropertyTest extends \Codeception\Test\Unit
{
    use TestTrait;

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
     * **Feature: architecture-refactoring, Property 1: Slug generation produces valid slugs**
     * **Validates: Requirements 1.1**
     * 
     * For any input string (including Cyrillic), the SlugService SHALL produce a slug 
     * containing only lowercase Latin letters, digits, and hyphens, 
     * with no leading/trailing hyphens.
     */
    public function testSlugContainsOnlyValidCharacters()
    {
        // Используем seq для генерации строк из разных наборов символов
        $this
            ->limitTo(100)
            ->minimumEvaluationRatio(0.01)
            ->forAll(
                Generators::seq(
                    Generators::elements(
                        'a', 'b', 'c', 'z', 'A', 'B', 'Z',
                        '0', '1', '9',
                        ' ', '-', '_', '!', '@', '#',
                        'а', 'б', 'в', 'я', 'А', 'Б', 'Я',
                        'ё', 'ж', 'щ', 'ь', 'ъ'
                    )
                )
            )
            ->then(function (array $chars) {
                $input = implode('', $chars);
                $slug = $this->slugService->createSlug($input);
                
                // Slug должен содержать только допустимые символы: a-z, 0-9, -
                // Пустой slug допустим для строк без букв/цифр
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9-]*$/',
                    $slug,
                    "Slug '$slug' contains invalid characters for input: " . substr($input, 0, 50)
                );
                
                // Проверяем свойства только для непустых slug
                if (strlen($slug) > 0) {
                    // Slug не должен начинаться с дефиса
                    $this->assertFalse(
                        str_starts_with($slug, '-'),
                        "Slug '$slug' should not start with hyphen"
                    );
                    
                    // Slug не должен заканчиваться дефисом
                    $this->assertFalse(
                        str_ends_with($slug, '-'),
                        "Slug '$slug' should not end with hyphen"
                    );
                    
                    // Slug не должен содержать двойных дефисов
                    $this->assertFalse(
                        str_contains($slug, '--'),
                        "Slug '$slug' should not contain consecutive hyphens"
                    );
                }
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 1: Slug generation produces valid slugs**
     * **Validates: Requirements 1.1**
     * 
     * For any Cyrillic string, the SlugService SHALL produce a non-empty slug
     * after transliteration (unless input contains only special characters).
     */
    public function testCyrillicTextProducesValidSlug()
    {
        // Генератор кириллических строк
        $cyrillicChars = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
        
        $this
            ->limitTo(100)
            ->forAll(
                Generators::string()
            )
            ->then(function (string $randomString) use ($cyrillicChars) {
                // Создаём кириллическую строку из случайных символов
                $cyrillicInput = '';
                $len = mb_strlen($randomString);
                $cyrillicLen = mb_strlen($cyrillicChars);
                
                for ($i = 0; $i < min($len, 20); $i++) {
                    $index = ord($randomString[$i] ?? 'a') % $cyrillicLen;
                    $cyrillicInput .= mb_substr($cyrillicChars, $index, 1);
                }
                
                if (empty($cyrillicInput)) {
                    return; // Пропускаем пустые строки
                }
                
                $slug = $this->slugService->createSlug($cyrillicInput);
                
                // Slug должен быть непустым для непустого кириллического ввода
                // (кроме случаев когда ввод состоит только из ъ и ь)
                $onlySoftHardSigns = preg_match('/^[ъьЪЬ]+$/', $cyrillicInput);
                if (!$onlySoftHardSigns) {
                    $this->assertNotEmpty(
                        $slug,
                        "Cyrillic input '$cyrillicInput' should produce non-empty slug"
                    );
                }
                
                // Slug должен содержать только допустимые символы
                $this->assertMatchesRegularExpression(
                    '/^[a-z0-9-]*$/',
                    $slug,
                    "Slug '$slug' from Cyrillic input contains invalid characters"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 1: Slug generation produces valid slugs**
     * **Validates: Requirements 1.1**
     * 
     * Slug generation is idempotent for already valid slugs.
     */
    public function testSlugGenerationIsIdempotent()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::string()
            )
            ->then(function (string $input) {
                $slug1 = $this->slugService->createSlug($input);
                $slug2 = $this->slugService->createSlug($slug1);
                
                // Применение createSlug к уже валидному slug должно давать тот же результат
                $this->assertSame(
                    $slug1,
                    $slug2,
                    "Slug generation should be idempotent: '$slug1' != '$slug2'"
                );
            });
    }

    /**
     * **Feature: architecture-refactoring, Property 1: Slug generation produces valid slugs**
     * **Validates: Requirements 1.1**
     * 
     * Slug is always lowercase.
     */
    public function testSlugIsAlwaysLowercase()
    {
        $this
            ->limitTo(100)
            ->forAll(
                Generators::string()
            )
            ->then(function (string $input) {
                $slug = $this->slugService->createSlug($input);
                
                $this->assertSame(
                    strtolower($slug),
                    $slug,
                    "Slug '$slug' should be lowercase"
                );
            });
    }
}

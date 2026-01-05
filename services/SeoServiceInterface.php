<?php

declare(strict_types=1);

namespace app\services;

use app\models\Category;
use app\models\Publication;
use app\models\Redirect;

/**
 * Интерфейс сервиса SEO.
 * Requirements: 8.1-8.10
 */
interface SeoServiceInterface
{
    /**
     * Получает глобальные SEO настройки.
     * 
     * @return array<string, mixed>
     */
    public function getGlobalSettings(): array;

    /**
     * Сохраняет глобальные SEO настройки.
     * 
     * @param array<string, mixed> $data
     */
    public function saveGlobalSettings(array $data): bool;

    /**
     * Получает мета-теги для сущности.
     * 
     * @return array<string, mixed>
     */
    public function getMetaTags(string $type, ?int $id = null): array;

    /**
     * Сохраняет мета-теги для сущности.
     * 
     * @param array<string, mixed> $data
     */
    public function saveMetaTags(string $type, int $id, array $data): bool;

    /**
     * Генерирует XML sitemap.
     * 
     * @return string Путь к сгенерированному файлу
     */
    public function generateSitemap(): string;

    /**
     * Получает настройки sitemap.
     * 
     * @return array<string, mixed>
     */
    public function getSitemapSettings(): array;

    /**
     * Сохраняет настройки sitemap.
     * 
     * @param array<string, mixed> $data
     */
    public function saveSitemapSettings(array $data): bool;

    /**
     * Получает содержимое robots.txt.
     */
    public function getRobotsContent(): string;

    /**
     * Сохраняет содержимое robots.txt.
     */
    public function saveRobotsContent(string $content): bool;

    /**
     * Находит редирект по исходному URL.
     */
    public function findRedirect(string $sourceUrl): ?Redirect;

    /**
     * Создаёт редирект.
     */
    public function createRedirect(string $source, string $target, int $type = 301): ?Redirect;

    /**
     * Получает JSON-LD разметку Article для публикации.
     * 
     * @return array<string, mixed>
     */
    public function getArticleSchema(Publication $publication): array;

    /**
     * Получает JSON-LD разметку WebSite для главной страницы.
     * 
     * @return array<string, mixed>
     */
    public function getWebsiteSchema(): array;

    /**
     * Получает JSON-LD разметку CollectionPage для категории.
     * 
     * @return array<string, mixed>
     */
    public function getCollectionSchema(Category $category): array;

    /**
     * Анализирует контент на соответствие SEO рекомендациям.
     * 
     * @return array<string, mixed> Результат анализа с рекомендациями
     */
    public function analyzeContent(string $title, string $description, string $content, ?string $keyword = null): array;

    /**
     * Получает canonical URL.
     */
    public function getCanonicalUrl(?string $customCanonical = null): string;
}

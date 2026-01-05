<?php

declare(strict_types=1);

namespace app\services;

use Yii;
use app\models\Category;
use app\models\Publication;
use app\models\Redirect;
use app\models\SeoSetting;
use yii\helpers\Url;

/**
 * Сервис для работы с SEO.
 * Requirements: 8.1-8.10
 */
class SeoService implements SeoServiceInterface
{
    public const META_TITLE_MAX_LENGTH = 60;
    public const META_DESCRIPTION_MIN_LENGTH = 120;
    public const META_DESCRIPTION_MAX_LENGTH = 160;
    public const MIN_CONTENT_WORDS = 300;

    private string $webPath;
    private string $baseUrl;

    public function __construct()
    {
        $this->webPath = Yii::getAlias('@webroot');
        $this->baseUrl = Yii::$app->request->hostInfo ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalSettings(): array
    {
        $setting = SeoSetting::getGlobal();
        if ($setting === null) {
            return [
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'og_image' => '',
            ];
        }

        return [
            'meta_title' => $setting->meta_title ?? '',
            'meta_description' => $setting->meta_description ?? '',
            'meta_keywords' => $setting->meta_keywords ?? '',
            'og_image' => $setting->og_image ?? '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveGlobalSettings(array $data): bool
    {
        $setting = SeoSetting::getOrCreateGlobal();
        $setting->meta_title = $data['meta_title'] ?? null;
        $setting->meta_description = $data['meta_description'] ?? null;
        $setting->meta_keywords = $data['meta_keywords'] ?? null;
        $setting->og_image = $data['og_image'] ?? null;

        return $setting->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTags(string $type, ?int $id = null): array
    {
        $setting = null;
        if ($id !== null) {
            $setting = SeoSetting::findByEntity($type, $id);
        }

        $global = SeoSetting::getGlobal();

        return [
            'meta_title' => $setting?->meta_title ?? $global?->meta_title ?? '',
            'meta_description' => $setting?->meta_description ?? $global?->meta_description ?? '',
            'meta_keywords' => $setting?->meta_keywords ?? $global?->meta_keywords ?? '',
            'og_title' => $setting?->og_title ?? $setting?->meta_title ?? '',
            'og_description' => $setting?->og_description ?? $setting?->meta_description ?? '',
            'og_image' => $setting?->og_image ?? $global?->og_image ?? '',
            'canonical_url' => $setting?->canonical_url ?? '',
            'robots' => $setting?->robots ?? 'index,follow',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveMetaTags(string $type, int $id, array $data): bool
    {
        $setting = SeoSetting::getOrCreateForEntity($type, $id);
        $setting->meta_title = $data['meta_title'] ?? null;
        $setting->meta_description = $data['meta_description'] ?? null;
        $setting->meta_keywords = $data['meta_keywords'] ?? null;
        $setting->og_title = $data['og_title'] ?? null;
        $setting->og_description = $data['og_description'] ?? null;
        $setting->og_image = $data['og_image'] ?? null;
        $setting->canonical_url = $data['canonical_url'] ?? null;
        $setting->robots = $data['robots'] ?? 'index,follow';

        return $setting->save();
    }


    /**
     * {@inheritdoc}
     */
    public function generateSitemap(): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        // Главная страница
        $this->addSitemapUrl($xml, Url::to(['/site/index'], true), date('c'), 'daily', '1.0');

        // Публикации
        $publications = Publication::find()
            ->where(['status' => 'published'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();

        foreach ($publications as $publication) {
            $this->addSitemapUrl(
                $xml,
                Url::to(['/publication/view', 'slug' => $publication->slug], true),
                date('c', strtotime($publication->updated_at)),
                'weekly',
                '0.8'
            );
        }

        // Категории
        $categories = Category::find()->all();
        foreach ($categories as $category) {
            $this->addSitemapUrl(
                $xml,
                Url::to(['/category/view', 'slug' => $category->slug], true),
                date('c', strtotime($category->updated_at ?? $category->created_at)),
                'weekly',
                '0.6'
            );
        }

        $xml->endElement(); // urlset
        $xml->endDocument();

        $content = $xml->outputMemory();
        $path = $this->webPath . '/sitemap.xml';
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * Добавляет URL в sitemap.
     */
    private function addSitemapUrl(\XMLWriter $xml, string $loc, string $lastmod, string $changefreq, string $priority): void
    {
        $xml->startElement('url');
        $xml->writeElement('loc', $loc);
        $xml->writeElement('lastmod', $lastmod);
        $xml->writeElement('changefreq', $changefreq);
        $xml->writeElement('priority', $priority);
        $xml->endElement();
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemapSettings(): array
    {
        $path = $this->webPath . '/sitemap.xml';
        return [
            'exists' => file_exists($path),
            'last_generated' => file_exists($path) ? date('Y-m-d H:i:s', filemtime($path)) : null,
            'path' => $path,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveSitemapSettings(array $data): bool
    {
        // Настройки sitemap можно хранить в params или отдельной таблице
        // Пока просто возвращаем true
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRobotsContent(): string
    {
        $path = $this->webPath . '/robots.txt';
        if (file_exists($path)) {
            return file_get_contents($path) ?: '';
        }

        // Базовое содержимое по умолчанию
        $sitemapUrl = $this->baseUrl . '/sitemap.xml';
        return "User-agent: *\nAllow: /\nSitemap: {$sitemapUrl}\n";
    }

    /**
     * {@inheritdoc}
     */
    public function saveRobotsContent(string $content): bool
    {
        $path = $this->webPath . '/robots.txt';
        $result = file_put_contents($path, $content);
        return $result !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function findRedirect(string $sourceUrl): ?Redirect
    {
        return Redirect::findBySourceUrl($sourceUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function createRedirect(string $source, string $target, int $type = 301): ?Redirect
    {
        $redirect = new Redirect([
            'source_url' => $source,
            'target_url' => $target,
            'type' => $type,
        ]);

        if ($redirect->save()) {
            return $redirect;
        }

        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getArticleSchema(Publication $publication): array
    {
        $author = $publication->author;
        $authorName = $author?->getDisplayName() ?? 'Автор';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $publication->title,
            'author' => [
                '@type' => 'Person',
                'name' => $authorName,
            ],
            'datePublished' => date('c', strtotime($publication->created_at)),
            'dateModified' => date('c', strtotime($publication->updated_at)),
            'image' => $publication->featured_image ? Url::to($publication->featured_image, true) : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => Yii::$app->name,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->baseUrl . '/images/logo.png',
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => Url::to(['/publication/view', 'slug' => $publication->slug], true),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => Yii::$app->name,
            'url' => $this->baseUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $this->baseUrl . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionSchema(Category $category): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $category->description ?? '',
            'url' => Url::to(['/category/view', 'slug' => $category->slug], true),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function analyzeContent(string $title, string $description, string $content, ?string $keyword = null): array
    {
        $issues = [];
        $warnings = [];
        $recommendations = [];
        $score = 100;

        // Анализ заголовка
        $titleLength = mb_strlen($title);
        if ($titleLength > self::META_TITLE_MAX_LENGTH) {
            $warnings[] = "Заголовок слишком длинный для поисковой выдачи ({$titleLength} символов, рекомендуется до " . self::META_TITLE_MAX_LENGTH . ")";
            $score -= 10;
        } elseif ($titleLength < 30) {
            $recommendations[] = "Заголовок слишком короткий ({$titleLength} символов)";
            $score -= 5;
        }

        // Анализ описания
        $descLength = mb_strlen($description);
        if ($descLength > self::META_DESCRIPTION_MAX_LENGTH) {
            $warnings[] = "Описание будет обрезано в поисковой выдаче ({$descLength} символов, максимум " . self::META_DESCRIPTION_MAX_LENGTH . ")";
            $score -= 10;
        } elseif ($descLength < self::META_DESCRIPTION_MIN_LENGTH && $descLength > 0) {
            $recommendations[] = "Описание слишком короткое ({$descLength} символов, рекомендуется от " . self::META_DESCRIPTION_MIN_LENGTH . ")";
            $score -= 5;
        } elseif ($descLength === 0) {
            $issues[] = "Описание не заполнено";
            $score -= 15;
        }

        // Анализ контента
        $wordCount = str_word_count(strip_tags($content), 0, 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя');
        if ($wordCount < self::MIN_CONTENT_WORDS) {
            $warnings[] = "Контент слишком короткий для хорошего ранжирования ({$wordCount} слов, рекомендуется от " . self::MIN_CONTENT_WORDS . ")";
            $score -= 15;
        }

        // Проверка наличия изображений
        if (strpos($content, '<img') === false) {
            $recommendations[] = "Добавьте изображение для лучшего отображения в соцсетях";
            $score -= 5;
        }

        // Проверка ключевого слова в заголовке
        if ($keyword !== null && $keyword !== '') {
            if (mb_stripos($title, $keyword) === false) {
                $recommendations[] = "Добавьте ключевое слово в заголовок";
                $score -= 10;
            }
        }

        // Определение общей оценки
        $score = max(0, $score);
        
        if ($score >= 80) {
            $rating = 'good';
        } elseif ($score >= 50) {
            $rating = 'average';
        } else {
            $rating = 'poor';
        }

        $ratingLabels = [
            'good' => 'Хорошо',
            'average' => 'Средне',
            'poor' => 'Плохо',
        ];

        return [
            'score' => $score,
            'rating' => $rating,
            'rating_label' => $ratingLabels[$rating],
            'issues' => $issues,
            'warnings' => $warnings,
            'recommendations' => $recommendations,
            'stats' => [
                'title_length' => $titleLength,
                'description_length' => $descLength,
                'word_count' => $wordCount,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalUrl(?string $customCanonical = null): string
    {
        if ($customCanonical !== null && $customCanonical !== '') {
            return $customCanonical;
        }

        return Url::canonical();
    }
}

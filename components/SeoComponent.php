<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use app\models\WebmasterVerification;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * Компонент для рендеринга SEO-тегов в layout.
 * Requirements: 8.1, 8.7, 8.8, 8.9
 */
class SeoComponent extends Component
{
    public string $title = '';
    public string $description = '';
    public string $keywords = '';
    public ?string $canonicalUrl = null;
    public ?string $robots = null;

    /** @var array<string, string> */
    public array $ogTags = [];

    /** @var array<string, mixed> */
    public array $schemaOrg = [];

    // Верификация вебмастеров
    public ?string $googleVerification = null;
    public ?string $yandexVerification = null;
    public ?string $bingVerification = null;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();
        $this->loadWebmasterVerifications();
    }

    /**
     * Загружает коды верификации вебмастеров из БД.
     */
    private function loadWebmasterVerifications(): void
    {
        $verifications = WebmasterVerification::findAllActive();
        foreach ($verifications as $verification) {
            switch ($verification->service) {
                case WebmasterVerification::SERVICE_GOOGLE:
                    $this->googleVerification = $verification->verification_code;
                    break;
                case WebmasterVerification::SERVICE_YANDEX:
                    $this->yandexVerification = $verification->verification_code;
                    break;
                case WebmasterVerification::SERVICE_BING:
                    $this->bingVerification = $verification->verification_code;
                    break;
            }
        }
    }

    /**
     * Устанавливает мета-теги из массива.
     * 
     * @param array<string, mixed> $tags
     */
    public function setMetaTags(array $tags): void
    {
        if (isset($tags['meta_title'])) {
            $this->title = $tags['meta_title'];
        }
        if (isset($tags['meta_description'])) {
            $this->description = $tags['meta_description'];
        }
        if (isset($tags['meta_keywords'])) {
            $this->keywords = $tags['meta_keywords'];
        }
        if (isset($tags['canonical_url'])) {
            $this->canonicalUrl = $tags['canonical_url'];
        }
        if (isset($tags['robots'])) {
            $this->robots = $tags['robots'];
        }
        if (isset($tags['og_title'])) {
            $this->ogTags['og:title'] = $tags['og_title'];
        }
        if (isset($tags['og_description'])) {
            $this->ogTags['og:description'] = $tags['og_description'];
        }
        if (isset($tags['og_image'])) {
            $this->ogTags['og:image'] = $tags['og_image'];
        }
    }

    /**
     * Устанавливает Schema.org разметку.
     * 
     * @param array<string, mixed> $schema
     */
    public function setSchemaOrg(array $schema): void
    {
        $this->schemaOrg = $schema;
    }

    /**
     * Регистрирует мета-теги в View.
     */
    public function registerMetaTags(View $view): void
    {
        // Title
        if ($this->title !== '') {
            $view->title = $this->title;
        }

        // Description
        if ($this->description !== '') {
            $view->registerMetaTag([
                'name' => 'description',
                'content' => $this->description,
            ], 'description');
        }

        // Keywords
        if ($this->keywords !== '') {
            $view->registerMetaTag([
                'name' => 'keywords',
                'content' => $this->keywords,
            ], 'keywords');
        }

        // Robots
        if ($this->robots !== null && $this->robots !== '') {
            $view->registerMetaTag([
                'name' => 'robots',
                'content' => $this->robots,
            ], 'robots');
        }

        // Open Graph tags
        foreach ($this->ogTags as $property => $content) {
            if ($content !== '' && $content !== null) {
                $view->registerMetaTag([
                    'property' => $property,
                    'content' => $content,
                ], $property);
            }
        }

        // OG type (default to website)
        if (!isset($this->ogTags['og:type'])) {
            $view->registerMetaTag([
                'property' => 'og:type',
                'content' => 'website',
            ], 'og:type');
        }

        // OG url
        if (!isset($this->ogTags['og:url'])) {
            $view->registerMetaTag([
                'property' => 'og:url',
                'content' => Yii::$app->request->absoluteUrl,
            ], 'og:url');
        }
    }

    /**
     * Регистрирует Schema.org JSON-LD разметку.
     */
    public function registerSchemaOrg(View $view): void
    {
        if (empty($this->schemaOrg)) {
            return;
        }

        $json = Json::encode($this->schemaOrg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $view->registerJs("", View::POS_HEAD, 'schema-org-placeholder');
        
        $script = Html::script($json, ['type' => 'application/ld+json']);
        $view->registerMetaTag(['name' => 'schema-org-json-ld'], 'schema-org');
        
        // Вставляем JSON-LD в head
        $view->on(View::EVENT_END_BODY, function () use ($view, $script) {
            echo $script;
        });
    }

    /**
     * Регистрирует canonical URL.
     */
    public function registerCanonical(View $view): void
    {
        $url = $this->canonicalUrl;
        if ($url === null || $url === '') {
            $url = Yii::$app->request->absoluteUrl;
        }

        $view->registerLinkTag([
            'rel' => 'canonical',
            'href' => $url,
        ], 'canonical');
    }

    /**
     * Регистрирует теги верификации вебмастеров.
     */
    public function registerWebmasterTags(View $view): void
    {
        if ($this->googleVerification !== null && $this->googleVerification !== '') {
            $view->registerMetaTag([
                'name' => 'google-site-verification',
                'content' => $this->googleVerification,
            ], 'google-verification');
        }

        if ($this->yandexVerification !== null && $this->yandexVerification !== '') {
            $view->registerMetaTag([
                'name' => 'yandex-verification',
                'content' => $this->yandexVerification,
            ], 'yandex-verification');
        }

        if ($this->bingVerification !== null && $this->bingVerification !== '') {
            $view->registerMetaTag([
                'name' => 'msvalidate.01',
                'content' => $this->bingVerification,
            ], 'bing-verification');
        }
    }

    /**
     * Регистрирует все SEO теги.
     */
    public function registerAll(View $view): void
    {
        $this->registerMetaTags($view);
        $this->registerCanonical($view);
        $this->registerWebmasterTags($view);
        $this->registerSchemaOrg($view);
    }
}

<?php

/** @var yii\web\View $this */
/** @var app\models\Publication $model */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->meta_title ?: $model->title;
$this->params['meta_description'] = $model->meta_description ?: $model->excerpt;

// Canonical URL
$canonicalUrl = Url::to(['/publication/view', 'slug' => $model->slug], true);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl]);

// Open Graph tags
$this->registerMetaTag(['property' => 'og:title', 'content' => $this->title]);
$this->registerMetaTag(['property' => 'og:description', 'content' => $this->params['meta_description']]);
$this->registerMetaTag(['property' => 'og:type', 'content' => 'article']);
$this->registerMetaTag(['property' => 'og:url', 'content' => $canonicalUrl]);
$this->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->name]);

if ($model->featured_image) {
    // Convert relative URL to absolute if needed
    $imageUrl = $model->featured_image;
    if (strpos($imageUrl, 'http') !== 0) {
        $imageUrl = Url::to($imageUrl, true);
    }
    $this->registerMetaTag(['property' => 'og:image', 'content' => $imageUrl]);
}

// Article specific Open Graph tags
if ($model->published_at) {
    $this->registerMetaTag(['property' => 'article:published_time', 'content' => date('c', strtotime($model->published_at))]);
}
if ($model->updated_at) {
    $this->registerMetaTag(['property' => 'article:modified_time', 'content' => date('c', strtotime($model->updated_at))]);
}

// Twitter Card tags
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->params['meta_description']]);
if ($model->featured_image) {
    $this->registerMetaTag(['name' => 'twitter:image', 'content' => $imageUrl ?? $model->featured_image]);
}
?>

<article class="publication-view bg-white rounded-lg shadow-sm p-6">
    <!-- Header -->
    <header class="mb-8">
        <?php if ($model->category): ?>
            <div class="mb-4">
                <a href="<?= Url::to(['/category/view', 'slug' => $model->category->slug]) ?>" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <?= Html::encode($model->category->name) ?>
                </a>
            </div>
        <?php endif; ?>

        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <?= Html::encode($model->title) ?>
        </h1>

        <div class="flex items-center gap-4 text-sm text-gray-500">
            <span><?= Yii::$app->formatter->asDate($model->published_at, 'long') ?></span>
            <span>&bull;</span>
            <span><?= Yii::$app->formatter->asInteger($model->views) ?> просмотров</span>
            <span>&bull;</span>
            <?= \app\widgets\FavoriteButton::widget(['publicationId' => $model->id]) ?>
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->canEditPublication($model)): ?>
                <span>&bull;</span>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'text-blue-600 hover:text-blue-800']) ?>
            <?php endif; ?>
        </div>
    </header>

    <!-- Featured Image -->
    <?php if ($model->featured_image): ?>
        <div class="mb-8">
            <img src="<?= Html::encode($model->featured_image) ?>" 
                 alt="<?= Html::encode($model->title) ?>"
                 class="w-full rounded-lg shadow-sm"
                 loading="lazy">
        </div>
    <?php endif; ?>

    <!-- Excerpt -->
    <?php if ($model->excerpt): ?>
        <div class="mb-8 text-xl text-gray-600 leading-relaxed border-l-4 border-blue-500 pl-4">
            <?= Html::encode($model->excerpt) ?>
        </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="prose prose-lg max-w-none mb-8">
        <?= $model->content ?>
    </div>

    <!-- Tags -->
    <?php if ($model->tags): ?>
        <div class="border-t border-gray-200 pt-6 mb-8">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Теги:</h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($model->tags as $tag): ?>
                    <a href="<?= Url::to(['/tag/view', 'slug' => $tag->slug]) ?>" 
                       class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 text-sm px-3 py-1 rounded-full transition-colors">
                        <?= Html::encode($tag->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back link -->
    <div class="border-t border-gray-200 pt-6 mb-8">
        <a href="<?= Url::to(['/publication/index']) ?>" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Вернуться к списку публикаций
        </a>
    </div>

    <!-- Comments Section -->
    <div class="mt-12">
        <?= \app\widgets\CommentForm::widget(['publicationId' => $model->id]) ?>
        <?= \app\widgets\CommentList::widget(['publicationId' => $model->id]) ?>
    </div>
</article>

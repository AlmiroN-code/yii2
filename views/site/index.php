<?php

/** @var yii\web\View $this */
/** @var app\models\Publication[] $publications */
/** @var array $settings */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

$this->title = $settings['title'] ?: Yii::$app->name;
?>

<?php if ($settings['hero_image'] || $settings['title']): ?>
<!-- Hero Section -->
<div class="relative -mx-4 sm:-mx-6 lg:-mx-8 mb-8 <?= $settings['hero_image'] ? 'py-16' : 'py-8' ?> <?= $settings['hero_image'] ? 'bg-gray-900' : 'bg-gradient-to-r from-blue-600 to-indigo-700' ?> rounded-lg overflow-hidden">
    <?php if ($settings['hero_image']): ?>
        <div class="absolute inset-0">
            <img src="<?= Html::encode($settings['hero_image']) ?>" alt="" class="w-full h-full object-cover opacity-40">
        </div>
    <?php endif; ?>
    <div class="relative text-center px-4">
        <?php if ($settings['title']): ?>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4"><?= Html::encode($settings['title']) ?></h1>
        <?php endif; ?>
        <?php if ($settings['subtitle']): ?>
            <p class="text-lg text-gray-200 max-w-2xl mx-auto"><?= Html::encode($settings['subtitle']) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Последние публикации</h2>
</div>

<?php if (!empty($publications)): ?>
<div class="grid gap-8">
    <?php foreach ($publications as $publication): ?>
    <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
        <div class="flex flex-col md:flex-row">
            <?php if ($publication->featured_image): ?>
                <div class="md:w-64 flex-shrink-0">
                    <a href="<?= Url::to(['/publication/view', 'slug' => $publication->slug]) ?>">
                        <img src="<?= Html::encode($publication->featured_image) ?>" 
                             alt="<?= Html::encode($publication->title) ?>"
                             class="w-full h-48 md:h-full object-cover"
                             loading="lazy">
                    </a>
                </div>
            <?php endif; ?>
            <div class="p-6 flex-1">
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-3">
                    <?php if ($publication->category): ?>
                        <a href="<?= Url::to(['/category/view', 'slug' => $publication->category->slug]) ?>" 
                           class="text-blue-600 hover:text-blue-800">
                            <?= Html::encode($publication->category->name) ?>
                        </a>
                    <?php endif; ?>
                    <span><?= Yii::$app->formatter->asDate($publication->published_at, 'long') ?></span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    <a href="<?= Url::to(['/publication/view', 'slug' => $publication->slug]) ?>" 
                       class="hover:text-blue-600">
                        <?= Html::encode($publication->title) ?>
                    </a>
                </h3>
                <p class="text-gray-600 mb-4">
                    <?= Html::encode($publication->excerpt ?: StringHelper::truncate(strip_tags($publication->content), 200)) ?>
                </p>
                <?php if ($publication->tags): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($publication->tags as $tag): ?>
                            <a href="<?= Url::to(['/tag/view', 'slug' => $tag->slug]) ?>" 
                               class="text-xs bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-1 rounded">
                                <?= Html::encode($tag->name) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<div class="mt-8 text-center">
    <a href="<?= Url::to(['/publication/index']) ?>" 
       class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
        Все публикации
        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </a>
</div>

<?php else: ?>
<div class="text-center py-12 bg-white rounded-lg shadow-sm">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
    </svg>
    <h3 class="mt-4 text-lg font-medium text-gray-900">Публикаций пока нет</h3>
    <p class="mt-2 text-sm text-gray-500">Скоро здесь появятся интересные статьи</p>
</div>
<?php endif; ?>

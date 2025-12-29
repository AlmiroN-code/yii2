<?php

/** @var yii\web\View $this */
/** @var app\models\Category $category */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\widgets\LinkPager;

$this->title = $category->name;
$this->params['meta_description'] = $category->description;
?>

<div class="category-view">
    <!-- Category Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <?= Html::encode($category->name) ?>
        </h1>
        <?php if ($category->description): ?>
            <p class="text-gray-600"><?= Html::encode($category->description) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($dataProvider->getCount() > 0): ?>
        <div class="grid gap-8">
            <?php foreach ($dataProvider->getModels() as $publication): ?>
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
                                <span><?= Yii::$app->formatter->asDate($publication->published_at, 'long') ?></span>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                <a href="<?= Url::to(['/publication/view', 'slug' => $publication->slug]) ?>" 
                                   class="hover:text-blue-600">
                                    <?= Html::encode($publication->title) ?>
                                </a>
                            </h2>
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

        <!-- Pagination -->
        <div class="mt-8">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'flex justify-center gap-2'],
                'linkOptions' => ['class' => 'px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50'],
                'activePageCssClass' => 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700',
                'disabledPageCssClass' => 'opacity-50 cursor-not-allowed',
            ]) ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500">В этой категории пока нет публикаций.</p>
        </div>
    <?php endif; ?>

    <!-- Back link -->
    <div class="mt-8">
        <a href="<?= Url::to(['/publication/index']) ?>" 
           class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Все публикации
        </a>
    </div>
</div>

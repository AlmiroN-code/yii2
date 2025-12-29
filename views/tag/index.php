<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Tag[] $tags */

$this->title = 'Теги';
?>

<div class="tag-index">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($tags)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-wrap gap-3">
                <?php foreach ($tags as $tag): ?>
                    <?php $count = $tag->getPublications()->where(['status' => 'published'])->count(); ?>
                    <a href="<?= Url::to(['/tag/view', 'slug' => $tag->slug]) ?>" 
                       class="inline-flex items-center gap-2 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 px-4 py-2 rounded-full transition-colors">
                        <span><?= Html::encode($tag->name) ?></span>
                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full"><?= $count ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <p class="text-gray-500">Теги пока не созданы.</p>
        </div>
    <?php endif; ?>
</div>

<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Favorite[] $favorites */

$this->title = 'Избранное - ' . $user->getDisplayName();
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-6">
        <?= Html::a('← Назад к профилю', ['profile/view', 'username' => $user->username], [
            'class' => 'text-blue-600 hover:text-blue-800',
        ]) ?>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Избранное</h1>

    <?php if (empty($favorites)): ?>
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <p class="text-gray-500">Пока ничего не добавлено в избранное.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($favorites as $favorite): ?>
                <?php $publication = $favorite->publication; ?>
                <?php if ($publication): ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <?php if ($publication->category): ?>
                                <a href="<?= Url::to(['/category/view', 'slug' => $publication->category->slug]) ?>" 
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    <?= Html::encode($publication->category->name) ?>
                                </a>
                            <?php endif; ?>
                            
                            <h2 class="text-lg font-semibold mt-1">
                                <?= Html::a(Html::encode($publication->title), 
                                    ['publication/view', 'slug' => $publication->slug],
                                    ['class' => 'text-gray-900 hover:text-blue-600']) ?>
                            </h2>
                            
                            <?php if ($publication->excerpt): ?>
                                <p class="text-gray-600 text-sm mt-2"><?= Html::encode(mb_substr($publication->excerpt, 0, 150)) ?>...</p>
                            <?php endif; ?>
                            
                            <div class="mt-3 text-xs text-gray-400">
                                Добавлено <?= Yii::$app->formatter->asRelativeTime($favorite->created_at) ?>
                            </div>
                        </div>
                        
                        <?php if ($publication->featured_image): ?>
                            <div class="ml-4 flex-shrink-0">
                                <img src="<?= Html::encode($publication->featured_image) ?>" 
                                     alt="" class="w-24 h-24 object-cover rounded">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id === $user->id): ?>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <?= \app\widgets\FavoriteButton::widget(['publicationId' => $publication->id, 'showCount' => false]) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

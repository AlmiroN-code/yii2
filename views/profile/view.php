<?php

use yii\helpers\Html;
use app\models\Publication;
use app\models\Favorite;
use app\enums\PublicationStatus;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$this->title = $user->getDisplayName();
$profile = $user->profile;

// Счетчики
$isOwner = !Yii::$app->user->isGuest && Yii::$app->user->id === $user->id;
$publicationsCount = Publication::find()
    ->where(['author_id' => $user->id])
    ->andFilterWhere(['status' => $isOwner ? null : PublicationStatus::PUBLISHED->value])
    ->count();
$favoritesCount = Favorite::find()->where(['user_id' => $user->id])->count();
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <?php if ($profile && $profile->avatar): ?>
                        <img src="<?= Html::encode($profile->getAvatarUrl()) ?>" 
                             alt="<?= Html::encode($user->username) ?>"
                             class="w-32 h-32 rounded-full object-cover">
                    <?php else: ?>
                        <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-4xl text-gray-600"><?= strtoupper(substr($user->username, 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900"><?= Html::encode($user->getDisplayName()) ?></h1>
                    <p class="text-gray-500">@<?= Html::encode($user->username) ?></p>
                    
                    <?php if ($profile && $profile->bio): ?>
                        <p class="mt-4 text-gray-700"><?= Html::encode($profile->bio) ?></p>
                    <?php endif; ?>

                    <p class="mt-4 text-sm text-gray-500">
                        На сайте с <?= Yii::$app->formatter->asDate($user->created_at, 'long') ?>
                    </p>

                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id === $user->id): ?>
                        <div class="mt-4 flex gap-2 justify-center sm:justify-start">
                            <?= Html::a('Редактировать профиль', ['profile/edit', 'username' => $user->username], [
                                'class' => 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50',
                            ]) ?>
                            <?= Html::a('Изменить пароль', ['profile/password', 'username' => $user->username], [
                                'class' => 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50',
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-t border-gray-200">
            <nav class="flex">
                <?php if ($user->canCreatePublication()): ?>
                    <?= Html::a('Публикации <span class="ml-1 px-2 py-0.5 text-xs bg-gray-200 rounded-full">' . $publicationsCount . '</span>', ['profile/publications', 'username' => $user->username], [
                        'class' => 'flex-1 py-4 px-6 text-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                    ]) ?>
                <?php endif; ?>
                <?= Html::a('Избранное <span class="ml-1 px-2 py-0.5 text-xs bg-gray-200 rounded-full">' . $favoritesCount . '</span>', ['profile/favorites', 'username' => $user->username], [
                    'class' => 'flex-1 py-4 px-6 text-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                ]) ?>
            </nav>
        </div>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\enums\PublicationStatus;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Публикации — ' . $user->getDisplayName();
$profile = $user->profile;
$isOwner = !Yii::$app->user->isGuest && Yii::$app->user->id === $user->id;
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
                             class="w-24 h-24 rounded-full object-cover">
                    <?php else: ?>
                        <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-3xl text-gray-600"><?= strtoupper(substr($user->username, 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900"><?= Html::encode($user->getDisplayName()) ?></h1>
                    <p class="text-gray-500">@<?= Html::encode($user->username) ?></p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-t border-gray-200">
            <nav class="flex">
                <?php if ($user->canCreatePublication()): ?>
                    <?= Html::a('Публикации', ['profile/publications', 'username' => $user->username], [
                        'class' => 'flex-1 py-4 px-6 text-center text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-blue-50',
                    ]) ?>
                <?php endif; ?>
                <?= Html::a('Избранное', ['profile/favorites', 'username' => $user->username], [
                    'class' => 'flex-1 py-4 px-6 text-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                ]) ?>
            </nav>
        </div>
    </div>

    <!-- Publications List -->
    <div class="mt-6">
        <?php if ($isOwner): ?>
            <div class="flex justify-end mb-4">
                <?= Html::a('Написать статью', ['/publication/create'], ['class' => 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700']) ?>
            </div>
        <?php endif; ?>

        <?php if ($dataProvider->getCount() > 0): ?>
            <div class="space-y-4">
                <?php foreach ($dataProvider->getModels() as $publication): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?php if ($publication->getPublicationStatus() === PublicationStatus::PUBLISHED): ?>
                                        <?= Html::a(Html::encode($publication->title), ['/publication/view', 'slug' => $publication->slug], ['class' => 'hover:text-blue-600']) ?>
                                    <?php else: ?>
                                        <?= Html::encode($publication->title) ?>
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full"><?= $publication->getStatusLabel() ?></span>
                                    <?php endif; ?>
                                </h3>
                                <?php if ($publication->excerpt): ?>
                                    <p class="mt-2 text-gray-600 text-sm"><?= Html::encode(mb_substr($publication->excerpt, 0, 200)) ?>...</p>
                                <?php endif; ?>
                                <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                                    <span><?= Yii::$app->formatter->asDate($publication->created_at, 'medium') ?></span>
                                    <span><?= Yii::$app->formatter->asInteger($publication->views) ?> просмотров</span>
                                    <?php if ($publication->category): ?>
                                        <span><?= Html::encode($publication->category->name) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($isOwner): ?>
                                <div class="ml-4">
                                    <?= Html::a('Редактировать', ['/publication/update', 'id' => $publication->id], ['class' => 'text-blue-600 hover:text-blue-800 text-sm']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                    'options' => ['class' => 'flex justify-center gap-1'],
                    'linkOptions' => ['class' => 'px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-gray-50'],
                    'activePageCssClass' => 'bg-blue-500 text-white',
                ]) ?>
            </div>
        <?php else: ?>
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <p class="text-gray-500">
                    <?= $isOwner ? 'У вас пока нет публикаций.' : 'У пользователя пока нет публикаций.' ?>
                </p>
                <?php if ($isOwner): ?>
                    <div class="mt-4">
                        <?= Html::a('Написать первую статью', ['/publication/create'], ['class' => 'inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

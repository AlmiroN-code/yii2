<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\enums\PublicationStatus;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $currentStatus */

$this->title = 'Мои публикации';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="publication-my max-w-6xl mx-auto py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Написать статью', ['create'], ['class' => 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700']) ?>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex gap-4">
            <?= Html::a('Все', ['my'], ['class' => 'px-3 py-1 rounded-md ' . ($currentStatus === null ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100')]) ?>
            <?= Html::a('Опубликованные', ['my', 'status' => PublicationStatus::PUBLISHED->value], ['class' => 'px-3 py-1 rounded-md ' . ($currentStatus === PublicationStatus::PUBLISHED->value ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:bg-gray-100')]) ?>
            <?= Html::a('Черновики', ['my', 'status' => PublicationStatus::DRAFT->value], ['class' => 'px-3 py-1 rounded-md ' . ($currentStatus === PublicationStatus::DRAFT->value ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600 hover:bg-gray-100')]) ?>
        </div>
    </div>

    <?php if ($dataProvider->getCount() > 0): ?>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Заголовок</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Просмотры</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Комментарии</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($dataProvider->getModels() as $publication): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= Html::a(Html::encode($publication->title), ['update', 'id' => $publication->id], ['class' => 'hover:text-blue-600']) ?>
                                </div>
                                <?php if ($publication->category): ?>
                                    <div class="text-sm text-gray-500"><?= Html::encode($publication->category->name) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    PublicationStatus::PUBLISHED->value => 'bg-green-100 text-green-800',
                                    PublicationStatus::DRAFT->value => 'bg-yellow-100 text-yellow-800',
                                    PublicationStatus::ARCHIVED->value => 'bg-gray-100 text-gray-800',
                                ];
                                $statusColor = $statusColors[$publication->status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColor ?>">
                                    <?= $publication->getStatusLabel() ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= Yii::$app->formatter->asDate($publication->created_at, 'short') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= Yii::$app->formatter->asInteger($publication->views) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $publication->getApprovedComments()->count() ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($publication->getPublicationStatus() === PublicationStatus::PUBLISHED): ?>
                                    <?= Html::a('Просмотр', ['view', 'slug' => $publication->slug], ['class' => 'text-gray-600 hover:text-gray-900 mr-3', 'target' => '_blank']) ?>
                                <?php endif; ?>
                                <?= Html::a('Редактировать', ['update', 'id' => $publication->id], ['class' => 'text-blue-600 hover:text-blue-900 mr-3']) ?>
                                <?= Html::a('Удалить', ['delete', 'id' => $publication->id], [
                                    'class' => 'text-red-600 hover:text-red-900',
                                    'data' => [
                                        'confirm' => 'Удалить публикацию "' . $publication->title . '"?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        <div class="mt-4">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->getPagination(),
                'options' => ['class' => 'flex justify-center gap-1'],
                'linkOptions' => ['class' => 'px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-gray-50'],
                'activePageCssClass' => 'bg-blue-500 text-white',
            ]) ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">У вас пока нет публикаций</h3>
            <p class="mt-1 text-sm text-gray-500">Начните с создания первой статьи.</p>
            <div class="mt-6">
                <?= Html::a('Написать статью', ['create'], ['class' => 'inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

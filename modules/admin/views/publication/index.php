<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\enums\PublicationStatus;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Публикации';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Публикации</h1>
        <p class="mt-1 text-sm text-gray-500">Управление публикациями сайта</p>
    </div>
    <div>
        <?= Html::a('Добавить публикацию', ['create'], [
            'class' => 'inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500'
        ]) ?>
    </div>
</div>

<div class="overflow-hidden bg-white shadow sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обложка</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Заголовок</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($dataProvider->getModels() as $model): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $model->id ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($model->featured_image): ?>
                        <img src="<?= Html::encode($model->featured_image) ?>" alt="" class="w-16 h-12 object-cover rounded">
                    <?php else: ?>
                        <div class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 max-w-xs">
                    <a href="<?= \yii\helpers\Url::to(['update', 'id' => $model->id]) ?>" class="text-sm font-medium text-gray-900 hover:text-indigo-600 line-clamp-2"><?= Html::encode($model->title) ?></a>
                    <div class="text-sm text-gray-500 truncate"><?= Html::encode($model->slug) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= $model->category ? Html::encode($model->category->name) : '-' ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($model->getPublicationStatus() === PublicationStatus::PUBLISHED): ?>
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                            <?= $model->getStatusLabel() ?>
                        </span>
                    <?php elseif ($model->getPublicationStatus() === PublicationStatus::ARCHIVED): ?>
                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                            <?= $model->getStatusLabel() ?>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                            <?= $model->getStatusLabel() ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= Yii::$app->formatter->asDate($model->created_at) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <?php if ($model->getPublicationStatus() === PublicationStatus::PUBLISHED): ?>
                        <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>', ['/publication/view', 'slug' => $model->slug], [
                            'class' => 'inline-flex text-green-600 hover:text-green-900 mr-3',
                            'title' => 'Просмотр',
                            'target' => '_blank',
                        ]) ?>
                    <?php endif; ?>
                    <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>', ['update', 'id' => $model->id], [
                        'class' => 'inline-flex text-indigo-600 hover:text-indigo-900 mr-3',
                        'title' => 'Редактировать',
                    ]) ?>
                    <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>', ['delete', 'id' => $model->id], [
                        'class' => 'inline-flex text-red-600 hover:text-red-900',
                        'title' => 'Удалить',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить эту публикацию?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($dataProvider->getModels())): ?>
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    Публикации не найдены
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
        'options' => ['class' => 'flex justify-center space-x-1'],
        'linkOptions' => ['class' => 'px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50'],
        'activePageCssClass' => 'bg-indigo-600 text-white border-indigo-600',
        'disabledPageCssClass' => 'opacity-50 cursor-not-allowed',
    ]) ?>
</div>

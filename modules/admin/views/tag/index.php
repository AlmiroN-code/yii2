<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Теги';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Теги</h1>
        <p class="mt-1 text-sm text-gray-500">Управление тегами публикаций</p>
    </div>
    <div>
        <?= Html::a('Добавить тег', ['create'], [
            'class' => 'inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500'
        ]) ?>
    </div>
</div>

<div class="overflow-hidden bg-white shadow sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($dataProvider->getModels() as $model): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $model->id ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-sm font-medium text-indigo-800">
                        <?= Html::encode($model->name) ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= Html::encode($model->slug) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?= Yii::$app->formatter->asDate($model->created_at) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>', ['update', 'id' => $model->id], [
                        'class' => 'inline-flex text-indigo-600 hover:text-indigo-900 mr-3',
                        'title' => 'Редактировать',
                    ]) ?>
                    <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>', ['delete', 'id' => $model->id], [
                        'class' => 'inline-flex text-red-600 hover:text-red-900',
                        'title' => 'Удалить',
                        'data' => [
                            'confirm' => 'Вы уверены? Связи с публикациями будут удалены.',
                            'method' => 'post',
                        ],
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($dataProvider->getModels())): ?>
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    Теги не найдены
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

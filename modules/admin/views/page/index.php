<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Статические страницы';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= Html::encode($this->title) ?></h1>
        <p class="mt-1 text-sm text-gray-500">Управление статическими страницами сайта</p>
    </div>
    <?= Html::a('Создать страницу', ['create'], ['class' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700']) ?>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'tableOptions' => ['class' => 'min-w-full divide-y divide-gray-200'],
        'headerRowOptions' => ['class' => 'bg-gray-50'],
        'rowOptions' => ['class' => 'hover:bg-gray-50'],
        'columns' => [
            [
                'attribute' => 'title',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'],
            ],
            [
                'attribute' => 'slug',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500'],
                'format' => 'raw',
                'value' => fn($model) => Html::a('/page/' . $model->slug, ['/page/view', 'slug' => $model->slug], ['class' => 'text-indigo-600 hover:text-indigo-900', 'target' => '_blank']),
            ],
            [
                'attribute' => 'is_active',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm'],
                'format' => 'raw',
                'value' => fn($model) => $model->is_active 
                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Активна</span>'
                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Скрыта</span>',
            ],
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['class' => 'px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium'],
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => fn($url) => Html::a('Редактировать', $url, ['class' => 'text-indigo-600 hover:text-indigo-900 mr-3']),
                    'delete' => fn($url) => Html::a('Удалить', $url, [
                        'class' => 'text-red-600 hover:text-red-900',
                        'data-method' => 'post',
                        'data-confirm' => 'Удалить эту страницу?',
                    ]),
                ],
            ],
        ],
    ]) ?>
</div>

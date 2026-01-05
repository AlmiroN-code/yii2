<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Редиректы';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Редиректы</h1>
        <p class="mt-1 text-sm text-gray-500">Управление 301/302 редиректами</p>
    </div>
    <a href="<?= Url::to(['create-redirect']) ?>" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Добавить редирект
    </a>
</div>

<!-- Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <nav class="flex flex-wrap">
        <a href="<?= Url::to(['index']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Основные
        </a>
        <a href="<?= Url::to(['sitemap']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Sitemap
        </a>
        <a href="<?= Url::to(['robots']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Robots.txt
        </a>
        <a href="<?= Url::to(['redirects']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
            Редиректы
        </a>
        <a href="<?= Url::to(['webmaster']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Вебмастер
        </a>
    </nav>
</div>

<div class="bg-white shadow sm:rounded-lg overflow-hidden">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'tableOptions' => ['class' => 'min-w-full divide-y divide-gray-200'],
        'headerRowOptions' => ['class' => 'bg-gray-50'],
        'rowOptions' => ['class' => 'hover:bg-gray-50'],
        'columns' => [
            [
                'attribute' => 'source_url',
                'label' => 'Исходный URL',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'],
                'format' => 'raw',
                'value' => fn($model) => '<code class="bg-gray-100 px-2 py-1 rounded text-xs">' . Html::encode($model->source_url) . '</code>',
            ],
            [
                'attribute' => 'target_url',
                'label' => 'Целевой URL',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-900'],
                'format' => 'raw',
                'value' => fn($model) => '<code class="bg-gray-100 px-2 py-1 rounded text-xs">' . Html::encode($model->target_url) . '</code>',
            ],
            [
                'attribute' => 'type',
                'label' => 'Тип',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm'],
                'format' => 'raw',
                'value' => fn($model) => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . 
                    ($model->type === 301 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') . '">' . 
                    $model->type . '</span>',
            ],
            [
                'attribute' => 'hits',
                'label' => 'Переходы',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500'],
            ],
            [
                'attribute' => 'is_active',
                'label' => 'Статус',
                'headerOptions' => ['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-sm'],
                'format' => 'raw',
                'value' => fn($model) => $model->is_active 
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Активен</span>'
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Неактивен</span>',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'headerOptions' => ['class' => 'px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider'],
                'contentOptions' => ['class' => 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium'],
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => fn($url, $model) => Html::a('Изменить', ['update-redirect', 'id' => $model->id], [
                        'class' => 'text-indigo-600 hover:text-indigo-900 mr-3',
                    ]),
                    'delete' => fn($url, $model) => Html::a('Удалить', ['delete-redirect', 'id' => $model->id], [
                        'class' => 'text-red-600 hover:text-red-900',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить этот редирект?',
                            'method' => 'post',
                        ],
                    ]),
                ],
            ],
        ],
    ]) ?>
</div>

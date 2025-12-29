<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Comment;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $currentStatus */

$this->title = 'Модерация комментариев';
?>

<div class="p-6">
    <h1 class="text-2xl font-bold mb-6"><?= Html::encode($this->title) ?></h1>

    <!-- Status filters -->
    <div class="mb-6 flex gap-2">
        <?= Html::a('Все', ['index'], [
            'class' => 'px-4 py-2 rounded ' . ($currentStatus === null ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'),
        ]) ?>
        <?php foreach (Comment::getStatusLabels() as $status => $label): ?>
            <?= Html::a($label, ['index', 'status' => $status], [
                'class' => 'px-4 py-2 rounded ' . ($currentStatus === $status ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'),
            ]) ?>
        <?php endforeach; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'min-w-full divide-y divide-gray-200'],
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm'],
            ],
            [
                'attribute' => 'publication_id',
                'label' => 'Публикация',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(Html::encode($model->publication->title ?? 'N/A'), 
                        ['/publication/view', 'slug' => $model->publication->slug ?? ''],
                        ['target' => '_blank', 'class' => 'text-blue-600 hover:underline']);
                },
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm'],
            ],
            [
                'label' => 'Автор',
                'value' => function($model) {
                    return $model->getAuthorName();
                },
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm'],
            ],
            [
                'attribute' => 'content',
                'value' => function($model) {
                    return mb_substr($model->content, 0, 100) . (mb_strlen($model->content) > 100 ? '...' : '');
                },
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm max-w-xs'],
            ],
            [
                'attribute' => 'rating',
                'format' => 'raw',
                'value' => function($model) {
                    return str_repeat('★', $model->rating) . str_repeat('☆', 5 - $model->rating);
                },
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm text-yellow-500'],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    $colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'spam' => 'bg-gray-100 text-gray-800',
                    ];
                    return '<span class="px-2 py-1 text-xs rounded ' . ($colors[$model->status] ?? '') . '">' . $model->getStatusLabel() . '</span>';
                },
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm text-gray-500'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{approve} {reject} {delete}',
                'buttons' => [
                    'approve' => function($url, $model) {
                        if ($model->status === Comment::STATUS_APPROVED) return '';
                        return Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>', ['approve', 'id' => $model->id], [
                            'class' => 'inline-flex text-green-600 hover:text-green-800 mr-2',
                            'title' => 'Одобрить',
                            'data-method' => 'post',
                        ]);
                    },
                    'reject' => function($url, $model) {
                        if ($model->status === Comment::STATUS_REJECTED) return '';
                        return Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>', ['reject', 'id' => $model->id], [
                            'class' => 'inline-flex text-orange-600 hover:text-orange-800 mr-2',
                            'title' => 'Отклонить',
                            'data-method' => 'post',
                        ]);
                    },
                    'delete' => function($url, $model) {
                        return Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>', ['delete', 'id' => $model->id], [
                            'class' => 'inline-flex text-red-600 hover:text-red-800',
                            'title' => 'Удалить',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить комментарий?',
                        ]);
                    },
                ],
                'headerOptions' => ['class' => 'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase'],
                'contentOptions' => ['class' => 'px-4 py-3 text-sm'],
            ],
        ],
    ]) ?>
</div>

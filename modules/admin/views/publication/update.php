<?php

/** @var yii\web\View $this */
/** @var app\models\Publication $model */
/** @var array $categories */
/** @var app\models\Tag[] $tags */

use yii\helpers\Html;

$this->title = 'Редактирование: ' . $model->title;
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Редактирование публикации</h1>
    <p class="mt-1 text-sm text-gray-500"><?= Html::encode($model->title) ?></p>
</div>

<?= $this->render('_form', [
    'model' => $model,
    'categories' => $categories,
    'tags' => $tags,
]) ?>

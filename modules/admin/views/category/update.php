<?php

/** @var yii\web\View $this */
/** @var app\models\Category $model */
/** @var array $parentCategories */

use yii\helpers\Html;

$this->title = 'Редактирование: ' . $model->name;
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Редактирование категории</h1>
    <p class="mt-1 text-sm text-gray-500"><?= Html::encode($model->name) ?></p>
</div>

<?= $this->render('_form', [
    'model' => $model,
    'parentCategories' => $parentCategories,
]) ?>

<?php

/** @var yii\web\View $this */
/** @var app\models\Category $model */
/** @var array $parentCategories */

$this->title = 'Создание категории';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?= $this->title ?></h1>
    <p class="mt-1 text-sm text-gray-500">Заполните форму для создания новой категории</p>
</div>

<?= $this->render('_form', [
    'model' => $model,
    'parentCategories' => $parentCategories,
]) ?>

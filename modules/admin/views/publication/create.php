<?php

/** @var yii\web\View $this */
/** @var app\models\Publication $model */
/** @var array $categories */
/** @var app\models\Tag[] $tags */

$this->title = 'Создание публикации';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?= $this->title ?></h1>
    <p class="mt-1 text-sm text-gray-500">Заполните форму для создания новой публикации</p>
</div>

<?= $this->render('_form', [
    'model' => $model,
    'categories' => $categories,
    'tags' => $tags,
]) ?>

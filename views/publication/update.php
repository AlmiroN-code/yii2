<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PublicationForm $model */
/** @var app\models\Publication $publication */
/** @var app\models\Category[] $categories */
/** @var app\models\Tag[] $tags */

$this->title = 'Редактирование: ' . $publication->title;
$this->params['breadcrumbs'][] = ['label' => 'Мои публикации', 'url' => ['my']];
$this->params['breadcrumbs'][] = ['label' => $publication->title, 'url' => ['view', 'slug' => $publication->slug]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="publication-update max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'tags' => $tags,
        'publication' => $publication,
    ]) ?>
</div>

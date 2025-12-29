<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Publication;

/** @var yii\web\View $this */
/** @var app\models\PublicationForm $model */
/** @var app\models\Category[] $categories */
/** @var app\models\Tag[] $tags */

$this->title = 'Новая публикация';
$this->params['breadcrumbs'][] = ['label' => 'Мои публикации', 'url' => ['my']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="publication-create max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'tags' => $tags,
    ]) ?>
</div>

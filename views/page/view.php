<?php

/** @var yii\web\View $this */
/** @var app\models\Page $model */

use yii\helpers\Html;

$this->title = $model->title;
?>

<article class="max-w-4xl mx-auto">
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= Html::encode($model->title) ?></h1>
    </header>

    <div class="prose prose-lg max-w-none">
        <?= $model->content ?>
    </div>
</article>

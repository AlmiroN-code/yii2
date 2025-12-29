<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Publication;

/** @var yii\web\View $this */
/** @var app\models\PublicationForm $model */
/** @var app\models\Category[] $categories */
/** @var app\models\Tag[] $tags */
/** @var app\models\Publication|null $publication */

$publication = $publication ?? null;
?>

<div class="bg-white rounded-lg shadow-sm p-6">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'space-y-6'],
    ]); ?>

    <?= $form->field($model, 'title', [
        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
    ])->textInput(['maxlength' => true, 'placeholder' => 'Введите заголовок']) ?>

    <?= $form->field($model, 'slug', [
        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
    ])->textInput(['maxlength' => true, 'placeholder' => 'Оставьте пустым для автогенерации'])
      ->hint('URL-адрес публикации. Оставьте пустым для автоматической генерации из заголовка.', ['class' => 'text-gray-500 text-sm mt-1']) ?>

    <?= $form->field($model, 'excerpt', [
        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
    ])->textarea(['rows' => 3, 'placeholder' => 'Краткое описание для превью']) ?>

    <?= $form->field($model, 'content', [
        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
    ])->textarea(['rows' => 15, 'placeholder' => 'Содержимое публикации']) ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?= $form->field($model, 'category_id', [
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->dropDownList(
            ArrayHelper::map($categories, 'id', 'name'),
            ['prompt' => 'Выберите категорию']
        ) ?>

        <?= $form->field($model, 'status', [
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->dropDownList(Publication::getStatusLabels()) ?>
    </div>

    <?= $form->field($model, 'tagIds', [
        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
    ])->checkboxList(
        ArrayHelper::map($tags, 'id', 'name'),
        ['class' => 'flex flex-wrap gap-4', 'itemOptions' => ['labelOptions' => ['class' => 'inline-flex items-center gap-2 text-sm text-gray-700']]]
    ) ?>

    <div>
        <?= $form->field($model, 'imageFile', [
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->fileInput() ?>
        
        <?php if ($publication && $publication->featured_image): ?>
            <div class="mt-2">
                <p class="text-sm text-gray-500 mb-2">Текущая обложка:</p>
                <img src="<?= Html::encode($publication->featured_image) ?>" alt="" class="max-w-xs rounded-lg shadow-sm">
            </div>
        <?php endif; ?>
    </div>

    <div class="flex gap-4 pt-4 border-t">
        <?= Html::submitButton('Сохранить', ['class' => 'px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) ?>
        <?= Html::a('Отмена', ['my'], ['class' => 'px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

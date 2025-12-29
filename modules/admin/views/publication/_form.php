<?php

/** @var yii\web\View $this */
/** @var app\models\Publication $model */
/** @var array $categories */
/** @var app\models\Tag[] $tags */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="bg-white shadow sm:rounded-lg">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Title -->
            <div class="sm:col-span-2">
                <?= $form->field($model, 'title', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <!-- Slug -->
            <div class="sm:col-span-2">
                <?= $form->field($model, 'slug', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => 'Оставьте пустым для автогенерации'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <!-- Category -->
            <div>
                <?= $form->field($model, 'category_id', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->dropDownList($categories, [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'prompt' => '-- Выберите категорию --'
                ]) ?>
            </div>

            <!-- Status -->
            <div>
                <?= $form->field($model, 'status', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->dropDownList($model::getStatusLabels(), [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                ]) ?>
            </div>

            <!-- Excerpt -->
            <div class="sm:col-span-2">
                <?= $form->field($model, 'excerpt', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->textarea([
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'rows' => 3
                ]) ?>
            </div>

            <!-- Content -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Контент</label>
                <div id="quill-editor"></div>
                <?= Html::activeHiddenInput($model, 'content', ['id' => 'content-editor']) ?>
                <?php if ($model->hasErrors('content')): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $model->getFirstError('content') ?></p>
                <?php endif; ?>
            </div>

            <!-- Featured Image -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Изображение</label>
                <?php if ($model->featured_image): ?>
                    <div class="mt-2 mb-2">
                        <img src="<?= $model->featured_image ?>" alt="Current image" class="h-32 w-auto rounded-md" loading="lazy">
                    </div>
                <?php endif; ?>
                <?= $form->field($model, 'featured_image', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'sr-only'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->fileInput([
                    'class' => 'mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100',
                    'accept' => 'image/*'
                ]) ?>
            </div>

            <!-- Tags -->
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Теги</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="Publication[tagIds][]" 
                                   value="<?= $tag->id ?>"
                                   <?= in_array($tag->id, $model->tagIds) ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700"><?= Html::encode($tag->name) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($tags)): ?>
                    <p class="text-sm text-gray-500">Теги не созданы. <?= Html::a('Создать тег', ['/admin/tag/create'], ['class' => 'text-indigo-600 hover:text-indigo-500']) ?></p>
                <?php endif; ?>
            </div>

            <!-- SEO Fields -->
            <div class="sm:col-span-2 border-t border-gray-200 pt-6 mt-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO настройки</h3>
            </div>

            <div class="sm:col-span-2">
                <?= $form->field($model, 'meta_title', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <div class="sm:col-span-2">
                <?= $form->field($model, 'meta_description', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->textarea([
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'rows' => 2
                ]) ?>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
        <?= Html::a('Отмена', ['index'], ['class' => 'inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 mr-3']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
// Quill Editor - free, no API key required
$this->registerCssFile('https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function() {
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Введите текст публикации...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    var contentField = document.getElementById('content-editor');
    if (contentField && contentField.value) {
        quill.root.innerHTML = contentField.value;
    }

    quill.on('text-change', function() {
        if (contentField) contentField.value = quill.root.innerHTML;
    });

    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (contentField) contentField.value = quill.root.innerHTML;
        });
    }
});
JS, \yii\web\View::POS_END);
?>

<style>
#quill-editor {
    height: 350px;
    background: white;
}
.ql-toolbar.ql-snow {
    border-radius: 0.375rem 0.375rem 0 0;
    border-color: #d1d5db;
}
.ql-container.ql-snow {
    border-radius: 0 0 0.375rem 0.375rem;
    border-color: #d1d5db;
}
</style>

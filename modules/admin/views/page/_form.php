<?php

/** @var yii\web\View $this */
/** @var app\models\Page $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="bg-white shadow sm:rounded-lg">
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <?= $form->field($model, 'title', [
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <div>
                <?= $form->field($model, 'slug', [
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => 'Оставьте пустым для автогенерации'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <div>
                <?= $form->field($model, 'content', [
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->textarea([
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'rows' => 15,
                    'id' => 'page-content',
                ]) ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <?= $form->field($model, 'is_active', [
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    ])->checkbox([
                        'class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500',
                    ]) ?>
                </div>

                <div>
                    <?= $form->field($model, 'sort_order', [
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                        'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'type' => 'number'],
                        'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                    ]) ?>
                </div>
            </div>

            <!-- SEO -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO настройки</h3>
            </div>

            <div>
                <?= $form->field($model, 'meta_title', [
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'maxlength' => 60],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->hint('Максимум 60 символов') ?>
            </div>

            <div>
                <?= $form->field($model, 'meta_description', [
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ])->textarea([
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'rows' => 2,
                    'maxlength' => 160,
                ])->hint('Рекомендуется 120-160 символов') ?>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
        <?= Html::a('Отмена', ['index'], ['class' => 'inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 mr-3']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

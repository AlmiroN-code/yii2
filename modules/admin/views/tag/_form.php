<?php

/** @var yii\web\View $this */
/** @var app\models\Tag $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="bg-white shadow sm:rounded-lg">
    <?php $form = ActiveForm::begin(); ?>
    
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Name -->
            <div>
                <?= $form->field($model, 'name', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ]) ?>
            </div>

            <!-- Slug -->
            <div>
                <?= $form->field($model, 'slug', [
                    'options' => ['class' => ''],
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700'],
                    'inputOptions' => ['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => 'Оставьте пустым для автогенерации'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
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

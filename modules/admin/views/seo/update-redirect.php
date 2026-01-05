<?php

/** @var yii\web\View $this */
/** @var app\models\Redirect $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать редирект';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Редактировать редирект</h1>
        <p class="mt-1 text-sm text-gray-500">Изменение редиректа #<?= $model->id ?></p>
    </div>
    <a href="<?= Url::to(['redirects']) ?>" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Назад к списку
    </a>
</div>

<?php $form = ActiveForm::begin(); ?>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            
            <!-- Source URL -->
            <div>
                <?= $form->field($model, 'source_url')->textInput([
                    'maxlength' => 500,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'placeholder' => '/old-page',
                ])->label('Исходный URL') ?>
            </div>

            <!-- Target URL -->
            <div>
                <?= $form->field($model, 'target_url')->textInput([
                    'maxlength' => 500,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'placeholder' => '/new-page',
                ])->label('Целевой URL') ?>
            </div>

            <!-- Type -->
            <div>
                <?= $form->field($model, 'type')->dropDownList([
                    301 => '301 (Постоянный)',
                    302 => '302 (Временный)',
                ], [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                ])->label('Тип редиректа') ?>
            </div>

            <!-- Is Active -->
            <div>
                <?= $form->field($model, 'is_active')->checkbox([
                    'class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                ])->label('Активен') ?>
            </div>

            <!-- Stats -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Статистика</h4>
                <p class="text-sm text-gray-600">Переходов: <span class="font-medium"><?= $model->hits ?></span></p>
                <p class="text-sm text-gray-600">Создан: <span class="font-medium"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span></p>
            </div>

        </div>
    </div>

    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
        <?= Html::a('Отмена', ['redirects'], ['class' => 'inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 mr-3']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

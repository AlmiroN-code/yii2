<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Вход';
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Вход в аккаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Или
                <?= Html::a('создайте новый аккаунт', ['auth/register'], ['class' => 'font-medium text-blue-600 hover:text-blue-500']) ?>
            </p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'mt-8 space-y-6'],
            'fieldConfig' => [
                'template' => "{input}\n{error}",
                'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
            ],
        ]); ?>

        <div class="rounded-md shadow-sm -space-y-px">
            <?= $form->field($model, 'identity')->textInput([
                'class' => 'appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm',
                'placeholder' => 'Имя пользователя или Email',
                'autofocus' => true,
            ]) ?>

            <?= $form->field($model, 'password')->passwordInput([
                'class' => 'appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm',
                'placeholder' => 'Пароль',
            ]) ?>
        </div>

        <div class="flex items-center justify-between">
            <?= $form->field($model, 'rememberMe', [
                'template' => '<div class="flex items-center">{input}<label for="loginform-rememberme" class="ml-2 block text-sm text-gray-900">Запомнить меня</label></div>',
            ])->checkbox([
                'class' => 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded',
            ], false) ?>
        </div>

        <div>
            <?= Html::submitButton('Войти', [
                'class' => 'group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход в админ-панель';
?>

<div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
        Вход в админ-панель
    </h2>
</div>

<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'space-y-6'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"mt-2\">{input}</div>\n{error}",
            'labelOptions' => ['class' => 'block text-sm font-medium leading-6 text-gray-900'],
            'inputOptions' => ['class' => 'block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'],
            'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Введите логин']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль']) ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => '<div class="flex items-center">{input}<label class="ml-2 block text-sm text-gray-900" for="loginform-rememberme">{labelTitle}</label></div>{error}',
        'class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600',
    ]) ?>

    <div>
        <?= Html::submitButton('Войти', [
            'class' => 'flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
            'name' => 'login-button'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

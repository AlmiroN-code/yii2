<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RegisterForm $model */

$this->title = 'Регистрация';
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Создать аккаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Уже есть аккаунт?
                <?= Html::a('Войдите', ['auth/login'], ['class' => 'font-medium text-blue-600 hover:text-blue-500']) ?>
            </p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'options' => ['class' => 'mt-8 space-y-6'],
            'fieldConfig' => [
                'template' => "<div class='mb-4'>{label}{input}{error}</div>",
                'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
            ],
        ]); ?>

        <?= $form->field($model, 'username')->textInput([
            'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'placeholder' => 'Латинские буквы, цифры, дефис',
            'autofocus' => true,
        ]) ?>

        <?= $form->field($model, 'email')->textInput([
            'type' => 'email',
            'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'placeholder' => 'example@mail.com',
        ]) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'placeholder' => 'Минимум 6 символов',
        ]) ?>

        <?= $form->field($model, 'password_confirm')->passwordInput([
            'class' => 'appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'placeholder' => 'Повторите пароль',
        ]) ?>

        <div>
            <?= Html::submitButton('Зарегистрироваться', [
                'class' => 'group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

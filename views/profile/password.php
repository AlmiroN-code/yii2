<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PasswordChangeForm $model */
/** @var app\models\User $user */

$this->title = 'Изменение пароля';
?>

<div class="max-w-md mx-auto py-8 px-4">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Изменение пароля</h1>

        <?php $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "<div class='mb-4'>{label}{input}{error}</div>",
                'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
            ],
        ]); ?>

        <?= $form->field($model, 'current_password')->passwordInput([
            'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
        ]) ?>

        <?= $form->field($model, 'new_password')->passwordInput([
            'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
        ])->hint('Минимум 6 символов') ?>

        <?= $form->field($model, 'confirm_password')->passwordInput([
            'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
        ]) ?>

        <div class="flex gap-4">
            <?= Html::submitButton('Изменить пароль', [
                'class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
            ]) ?>
            <?= Html::a('Отмена', ['profile/view', 'username' => $user->username], [
                'class' => 'inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProfileEditForm $model */
/** @var app\models\User $user */

$this->title = 'Редактирование профиля';
?>

<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Редактирование профиля</h1>

        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'template' => "<div class='mb-4'>{label}{input}{error}</div>",
                'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
            ],
        ]); ?>

        <!-- Current Avatar -->
        <?php $profile = $model->getProfile(); ?>
        <?php if ($profile->avatar): ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Текущий аватар</label>
                <img src="<?= Html::encode($profile->getAvatarUrl()) ?>" 
                     alt="Avatar" class="w-24 h-24 rounded-full object-cover">
            </div>
        <?php endif; ?>

        <?= $form->field($model, 'avatarFile')->fileInput([
            'class' => 'block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100',
            'accept' => 'image/*',
        ])->hint('JPG, PNG, GIF или WebP. Максимум 2MB.') ?>

        <?= $form->field($model, 'display_name')->textInput([
            'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'placeholder' => 'Как вас называть?',
        ]) ?>

        <?= $form->field($model, 'bio')->textarea([
            'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
            'rows' => 4,
            'placeholder' => 'Расскажите о себе...',
        ]) ?>

        <div class="flex gap-4">
            <?= Html::submitButton('Сохранить', [
                'class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
            ]) ?>
            <?= Html::a('Отмена', ['profile/view', 'username' => $user->username], [
                'class' => 'inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

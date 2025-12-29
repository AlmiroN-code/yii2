<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var array $roles */
/** @var array $statuses */

$this->title = 'Редактирование: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>

<div class="user-update">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'space-y-6'],
        ]); ?>

        <?= $form->field($model, 'username', [
            'options' => ['class' => ''],
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email', [
            'options' => ['class' => ''],
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'role', [
            'options' => ['class' => ''],
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->dropDownList($roles) ?>

        <?= $form->field($model, 'status', [
            'options' => ['class' => ''],
            'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
            'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
            'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
        ])->dropDownList($statuses) ?>

        <div class="flex gap-4 pt-4">
            <?= Html::submitButton('Сохранить', ['class' => 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) ?>
            <?= Html::a('Отмена', ['index'], ['class' => 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

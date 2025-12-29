<?php

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 font-medium">Спасибо за обращение! Мы ответим вам в ближайшее время.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Напишите нам</h2>
                    
                    <?php $form = ActiveForm::begin([
                        'id' => 'contact-form',
                        'options' => ['class' => 'space-y-6'],
                    ]); ?>

                    <?= $form->field($model, 'name', [
                        'options' => ['class' => ''],
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500', 'autofocus' => true],
                        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
                    ])->textInput()->label('Ваше имя') ?>

                    <?= $form->field($model, 'email', [
                        'options' => ['class' => ''],
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
                        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
                    ])->label('Email') ?>

                    <?= $form->field($model, 'subject', [
                        'options' => ['class' => ''],
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
                        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
                    ])->label('Тема') ?>

                    <?= $form->field($model, 'body', [
                        'options' => ['class' => ''],
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                        'inputOptions' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
                        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
                    ])->textarea(['rows' => 6])->label('Сообщение') ?>

                    <?= $form->field($model, 'verifyCode', [
                        'options' => ['class' => ''],
                        'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                        'errorOptions' => ['class' => 'text-red-500 text-sm mt-1'],
                    ])->widget(Captcha::class, [
                        'template' => '<div class="flex items-center gap-4">{image}<div class="flex-1">{input}</div></div>',
                        'options' => ['class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'],
                    ])->label('Проверочный код') ?>

                    <div class="pt-4">
                        <?= Html::submitButton('Отправить', [
                            'class' => 'w-full sm:w-auto px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors',
                            'name' => 'contact-button'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Контактная информация</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Email</p>
                                <a href="mailto:info@example.com" class="text-sm text-blue-600 hover:text-blue-800">info@example.com</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Телефон</p>
                                <a href="tel:+71234567890" class="text-sm text-blue-600 hover:text-blue-800">+7 (123) 456-78-90</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Адрес</p>
                                <p class="text-sm text-gray-600">г. Москва, ул. Примерная, д. 1</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

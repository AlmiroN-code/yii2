<?php

/** @var yii\web\View $this */
/** @var app\models\SeoSetting $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'SEO настройки';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SEO настройки</h1>
        <p class="mt-1 text-sm text-gray-500">Глобальные настройки поисковой оптимизации</p>
    </div>
</div>

<!-- Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <nav class="flex flex-wrap">
        <a href="<?= Url::to(['index']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Основные
        </a>
        <a href="<?= Url::to(['sitemap']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Sitemap
        </a>
        <a href="<?= Url::to(['robots']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Robots.txt
        </a>
        <a href="<?= Url::to(['redirects']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
            Редиректы
        </a>
        <a href="<?= Url::to(['webmaster']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Вебмастер
        </a>
    </nav>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            
            <!-- Meta Title -->
            <div>
                <?= $form->field($model, 'meta_title')->textInput([
                    'maxlength' => 255,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'placeholder' => 'Название сайта для поисковых систем',
                ])->label('Meta Title (заголовок сайта)') ?>
                <p class="mt-1 text-sm text-gray-500">Рекомендуемая длина: до 60 символов</p>
            </div>

            <!-- Meta Description -->
            <div>
                <?= $form->field($model, 'meta_description')->textarea([
                    'rows' => 3,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'placeholder' => 'Описание сайта для поисковых систем',
                ])->label('Meta Description (описание)') ?>
                <p class="mt-1 text-sm text-gray-500">Рекомендуемая длина: 120-160 символов</p>
            </div>

            <!-- Meta Keywords -->
            <div>
                <?= $form->field($model, 'meta_keywords')->textInput([
                    'maxlength' => 500,
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                    'placeholder' => 'ключевое слово 1, ключевое слово 2',
                ])->label('Meta Keywords (ключевые слова)') ?>
                <p class="mt-1 text-sm text-gray-500">Через запятую (не обязательно, большинство поисковиков игнорируют)</p>
            </div>

            <!-- OG Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700">OG Image (изображение для соцсетей)</label>
                <?php if ($model->og_image): ?>
                    <div class="mt-2 mb-2">
                        <img src="<?= Html::encode($model->og_image) ?>" alt="OG Image" class="h-32 w-auto rounded-lg object-cover" loading="lazy">
                    </div>
                <?php endif; ?>
                <?= Html::activeFileInput($model, 'og_image', [
                    'accept' => 'image/*',
                    'class' => 'mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100',
                ]) ?>
                <p class="mt-1 text-sm text-gray-500">Рекомендуемый размер: 1200x630 пикселей</p>
            </div>

        </div>
    </div>

    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

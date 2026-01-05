<?php

/** @var yii\web\View $this */
/** @var array $settings */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Sitemap';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Sitemap</h1>
        <p class="mt-1 text-sm text-gray-500">Генерация XML карты сайта</p>
    </div>
</div>

<!-- Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <nav class="flex flex-wrap">
        <a href="<?= Url::to(['index']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Основные
        </a>
        <a href="<?= Url::to(['sitemap']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50">
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

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        
        <!-- Status -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Статус Sitemap</h3>
            
            <?php if ($settings['exists']): ?>
                <div class="flex items-center gap-3 p-4 bg-green-50 rounded-lg">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Sitemap существует</p>
                        <p class="text-sm text-green-600">Последняя генерация: <?= Html::encode($settings['last_generated']) ?></p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="/sitemap.xml" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-sm">
                        Открыть sitemap.xml →
                    </a>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-3 p-4 bg-yellow-50 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Sitemap не найден</p>
                        <p class="text-sm text-yellow-600">Нажмите кнопку ниже для генерации</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Generate Button -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Генерация</h3>
            <p class="text-sm text-gray-500 mb-4">
                Sitemap включает все опубликованные публикации, категории и статические страницы.
            </p>
            
            <?= Html::beginForm(['generate-sitemap'], 'post') ?>
                <?= Html::submitButton('Сгенерировать Sitemap', [
                    'class' => 'inline-flex items-center gap-2 rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700',
                ]) ?>
            <?= Html::endForm() ?>
        </div>

    </div>
</div>

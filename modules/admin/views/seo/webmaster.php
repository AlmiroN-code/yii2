<?php

/** @var yii\web\View $this */
/** @var app\models\WebmasterVerification $google */
/** @var app\models\WebmasterVerification $yandex */
/** @var app\models\WebmasterVerification $bing */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Вебмастер-сервисы';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Вебмастер-сервисы</h1>
        <p class="mt-1 text-sm text-gray-500">Коды верификации для поисковых систем</p>
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
        <a href="<?= Url::to(['webmaster']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Вебмастер
        </a>
    </nav>
</div>

<?= Html::beginForm('', 'post') ?>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6">
            
            <!-- Google Search Console -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-8 h-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Google Search Console</h3>
                        <p class="text-sm text-gray-500">Мета-тег: google-site-verification</p>
                    </div>
                </div>
                <input type="text" name="google_code" 
                       value="<?= Html::encode($google->verification_code ?? '') ?>"
                       placeholder="Введите код верификации Google"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <p class="mt-2 text-sm text-gray-500">
                    Получите код в <a href="https://search.google.com/search-console" target="_blank" class="text-indigo-600 hover:text-indigo-500">Google Search Console</a> → Настройки → Подтверждение права собственности → HTML-тег
                </p>
            </div>

            <!-- Yandex Webmaster -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-8 h-8 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2 12C2 6.48 6.48 2 12 2s10 4.48 10 10-4.48 10-10 10S2 17.52 2 12zm9.5-6.5v13l5-6.5-5-6.5z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Яндекс.Вебмастер</h3>
                        <p class="text-sm text-gray-500">Мета-тег: yandex-verification</p>
                    </div>
                </div>
                <input type="text" name="yandex_code" 
                       value="<?= Html::encode($yandex->verification_code ?? '') ?>"
                       placeholder="Введите код верификации Яндекс"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <p class="mt-2 text-sm text-gray-500">
                    Получите код в <a href="https://webmaster.yandex.ru" target="_blank" class="text-indigo-600 hover:text-indigo-500">Яндекс.Вебмастер</a> → Права доступа → Подтверждение прав → Мета-тег
                </p>
            </div>

            <!-- Bing Webmaster -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-8 h-8 text-teal-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5 3v16.5l4 2.5v-6l6 3.5 4-2.5V9.5L5 3zm4 14.5v-9l6 3.5v4l-6-1.5v3z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Bing Webmaster Tools</h3>
                        <p class="text-sm text-gray-500">Мета-тег: msvalidate.01</p>
                    </div>
                </div>
                <input type="text" name="bing_code" 
                       value="<?= Html::encode($bing->verification_code ?? '') ?>"
                       placeholder="Введите код верификации Bing"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <p class="mt-2 text-sm text-gray-500">
                    Получите код в <a href="https://www.bing.com/webmasters" target="_blank" class="text-indigo-600 hover:text-indigo-500">Bing Webmaster Tools</a> → Добавить сайт → XML-файл или мета-тег
                </p>
            </div>

        </div>
    </div>

    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>
</div>

<?= Html::endForm() ?>

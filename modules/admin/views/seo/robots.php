<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Robots.txt';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Robots.txt</h1>
        <p class="mt-1 text-sm text-gray-500">Управление файлом robots.txt</p>
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
        <a href="<?= Url::to(['robots']) ?>" class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50">
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

<?= Html::beginForm('', 'post') ?>

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        
        <div class="mb-4">
            <label for="robots_content" class="block text-sm font-medium text-gray-700 mb-2">Содержимое robots.txt</label>
            <textarea name="robots_content" id="robots_content" rows="15"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"><?= Html::encode($content) ?></textarea>
        </div>

        <div class="bg-blue-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Справка по директивам</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li><code class="bg-blue-100 px-1 rounded">User-agent: *</code> — применяется ко всем роботам</li>
                <li><code class="bg-blue-100 px-1 rounded">Disallow: /admin/</code> — запретить индексацию /admin/</li>
                <li><code class="bg-blue-100 px-1 rounded">Allow: /</code> — разрешить индексацию всего сайта</li>
                <li><code class="bg-blue-100 px-1 rounded">Sitemap: URL</code> — указать путь к sitemap</li>
            </ul>
        </div>

    </div>

    <div class="bg-gray-50 px-4 py-3 flex justify-between items-center sm:px-6 rounded-b-lg">
        <a href="/robots.txt" target="_blank" class="text-indigo-600 hover:text-indigo-500 text-sm">
            Открыть robots.txt →
        </a>
        <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
    </div>
</div>

<?= Html::endForm() ?>

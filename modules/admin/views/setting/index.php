<?php

/** @var yii\web\View $this */
/** @var string $tab */
/** @var array $settings */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Настройки сайта';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Настройки сайта</h1>
        <p class="mt-1 text-sm text-gray-500">Основные настройки вашего сайта</p>
    </div>
</div>

<!-- Tabs -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <nav class="flex">
        <a href="<?= Url::to(['index', 'tab' => 'general']) ?>" 
           class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 <?= $tab === 'general' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Основные
        </a>
        <a href="<?= Url::to(['index', 'tab' => 'homepage']) ?>" 
           class="flex items-center gap-2 px-6 py-4 text-sm font-medium border-b-2 <?= $tab === 'homepage' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Главная страница
        </a>
    </nav>
</div>

<?php if ($tab === 'general'): ?>
<!-- General Settings -->
<form method="post" enctype="multipart/form-data">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6">
                
                <!-- Site Name -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Название сайта</label>
                    <input type="text" name="site_name" id="site_name" 
                           value="<?= Html::encode($settings['site_name']) ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Append Site Name to Title -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="append_site_name" value="1"
                               <?= ($settings['append_site_name'] ?? '0') === '1' ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Добавлять в тег title страницы название сайта</span>
                    </label>
                    <p class="mt-1 text-sm text-gray-500">Например: "Заголовок страницы | Название сайта"</p>
                </div>

                <!-- Site Description -->
                <div>
                    <label for="site_description" class="block text-sm font-medium text-gray-700">Описание сайта</label>
                    <textarea name="site_description" id="site_description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?= Html::encode($settings['site_description']) ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">Используется в мета-тегах для SEO</p>
                </div>

                <!-- Site Keywords -->
                <div>
                    <label for="site_keywords" class="block text-sm font-medium text-gray-700">Ключевые слова</label>
                    <input type="text" name="site_keywords" id="site_keywords" 
                           value="<?= Html::encode($settings['site_keywords']) ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Через запятую, например: блог, статьи, новости</p>
                </div>

                <!-- Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Логотип</label>
                    <?php if ($settings['site_logo']): ?>
                        <div class="mt-2 mb-2">
                            <img src="<?= Html::encode($settings['site_logo']) ?>" alt="Logo" class="h-16 w-auto" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="site_logo" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500">Рекомендуемый размер: 200x50 пикселей</p>
                </div>

                <!-- Favicon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Фавикон</label>
                    <?php if ($settings['site_favicon']): ?>
                        <div class="mt-2 mb-2">
                            <img src="<?= Html::encode($settings['site_favicon']) ?>" alt="Favicon" class="h-8 w-8" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="site_favicon" accept="image/*,.ico"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500">Рекомендуемый размер: 32x32 или 64x64 пикселей (ICO, PNG)</p>
                </div>

            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
            <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
        </div>
    </div>
</form>

<?php elseif ($tab === 'homepage'): ?>
<!-- Homepage Settings -->
<form method="post" enctype="multipart/form-data">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6">
                
                <!-- Homepage Title -->
                <div>
                    <label for="homepage_title" class="block text-sm font-medium text-gray-700">Заголовок главной страницы</label>
                    <input type="text" name="homepage_title" id="homepage_title" 
                           value="<?= Html::encode($settings['homepage_title']) ?>"
                           placeholder="Добро пожаловать на наш блог"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Homepage Subtitle -->
                <div>
                    <label for="homepage_subtitle" class="block text-sm font-medium text-gray-700">Подзаголовок</label>
                    <textarea name="homepage_subtitle" id="homepage_subtitle" rows="2"
                              placeholder="Интересные статьи и публикации"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?= Html::encode($settings['homepage_subtitle']) ?></textarea>
                </div>

                <!-- Hero Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Фоновое изображение (Hero)</label>
                    <?php if ($settings['homepage_hero_image']): ?>
                        <div class="mt-2 mb-2">
                            <img src="<?= Html::encode($settings['homepage_hero_image']) ?>" alt="Hero" class="h-32 w-auto rounded-lg object-cover" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="homepage_hero_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500">Рекомендуемый размер: 1920x600 пикселей</p>
                </div>

                <!-- Featured Publications Count -->
                <div>
                    <label for="homepage_featured_count" class="block text-sm font-medium text-gray-700">Количество публикаций на главной</label>
                    <select name="homepage_featured_count" id="homepage_featured_count"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <?php foreach ([3, 6, 9, 12] as $count): ?>
                            <option value="<?= $count ?>" <?= $settings['homepage_featured_count'] == $count ? 'selected' : '' ?>><?= $count ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Show Categories -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="homepage_show_categories" value="1"
                               <?= ($settings['homepage_show_categories'] ?? '1') === '1' ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Показывать блок категорий</span>
                    </label>
                </div>

                <!-- Show Tags -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="homepage_show_tags" value="1"
                               <?= ($settings['homepage_show_tags'] ?? '1') === '1' ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Показывать облако тегов</span>
                    </label>
                </div>

            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
            <?= Html::submitButton('Сохранить', ['class' => 'inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700']) ?>
        </div>
    </div>
</form>
<?php endif; ?>

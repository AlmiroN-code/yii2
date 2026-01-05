<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\models\Category;
use app\models\Setting;
use app\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

// Get site settings
$siteName = Setting::get('site_name', Yii::$app->name);
$siteDescription = Setting::get('site_description', '');
$siteKeywords = Setting::get('site_keywords', '');
$siteLogo = Setting::get('site_logo', '');
$siteFavicon = Setting::get('site_favicon', '');
$appendSiteName = Setting::get('append_site_name', '0') === '1';

// Build page title
$pageTitle = $this->title;
if ($appendSiteName && $pageTitle && $pageTitle !== $siteName) {
    $pageTitle = $pageTitle . ' | ' . $siteName;
} elseif (!$pageTitle) {
    $pageTitle = $siteName;
}

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? $siteDescription]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? $siteKeywords]);

// SEO Component - регистрация мета-тегов и Schema.org
if (Yii::$app->has('seo')) {
    Yii::$app->seo->registerAll($this);
}

// Favicon
if ($siteFavicon) {
    $this->registerLinkTag(['rel' => 'icon', 'href' => $siteFavicon]);
} else {
    $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
}

// Fetch categories and tags for sidebar
$showCategories = Setting::get('homepage_show_categories', '1') === '1';
$showTags = Setting::get('homepage_show_tags', '1') === '1';
$categories = $showCategories ? Category::find()->where(['parent_id' => null])->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])->all() : [];
$tags = $showTags ? Tag::find()->all() : [];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full bg-gray-50">
<head>
    <title><?= Html::encode($pageTitle) ?></title>
    <?php $this->head() ?>
</head>
<body class="h-full">
<?php $this->beginBody() ?>

<div class="min-h-full flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Top row: Logo + Search + Auth -->
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <a href="<?= Url::home() ?>" class="flex items-center text-xl font-bold text-gray-900 hover:text-blue-600">
                        <?php if ($siteLogo): ?>
                            <img src="<?= Html::encode($siteLogo) ?>" alt="<?= Html::encode($siteName) ?>" class="max-h-12 w-auto object-contain" loading="lazy">
                        <?php else: ?>
                            <?= Html::encode($siteName) ?>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" 
                               class="search-autocomplete w-48 px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Поиск...">
                    </div>
                    <!-- Auth -->
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="<?= Url::to(['/auth/login']) ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                            Войти
                        </a>
                        <a href="<?= Url::to(['/auth/register']) ?>" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-blue-700">
                            Регистрация
                        </a>
                    <?php else: ?>
                        <?php $currentUser = Yii::$app->user->identity; ?>
                        <?php $userProfile = $currentUser->profile; ?>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
                                <?php if ($userProfile && $userProfile->avatar): ?>
                                    <img src="<?= Html::encode($userProfile->getAvatarUrl()) ?>" alt="" class="w-8 h-8 rounded-full object-cover">
                                <?php else: ?>
                                    <img src="<?= Yii::getAlias('@web/images/default-avatar.svg') ?>" alt="" class="w-8 h-8 rounded-full">
                                <?php endif; ?>
                                <?= Html::encode($currentUser->username) ?>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden group-hover:block">
                                <a href="<?= Url::to(['/profile/view', 'username' => $currentUser->username]) ?>" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Мой профиль
                                </a>
                                <?php if ($currentUser->canCreatePublication()): ?>
                                    <a href="<?= Url::to(['/publication/my']) ?>" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Мои публикации
                                    </a>
                                    <a href="<?= Url::to(['/publication/create']) ?>" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Написать статью
                                    </a>
                                <?php endif; ?>
                                <?php if ($currentUser->isAdmin()): ?>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="<?= Url::to(['/admin']) ?>" 
                                       class="block px-4 py-2 text-sm text-purple-600 hover:bg-gray-100">
                                        Админ-панель
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-100 my-1"></div>
                                <?= Html::beginForm(['/auth/logout'], 'post') ?>
                                    <?= Html::submitButton('Выйти', ['class' => 'block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 bg-transparent border-0 cursor-pointer']) ?>
                                <?= Html::endForm() ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Bottom row: Navigation -->
            <nav class="flex items-center justify-between py-3 border-t border-gray-100">
                <div class="flex items-center space-x-6">
                    <a href="<?= Url::to(['/publication/index']) ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Публикации
                    </a>
                    <?php if (!empty($categories)): ?>
                    <div class="relative group">
                        <button class="text-gray-600 hover:text-gray-900 text-sm font-medium flex items-center">
                            Категории
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                            <?php foreach ($categories as $category): ?>
                            <a href="<?= Url::to(['/category/view', 'slug' => $category->slug]) ?>" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <?= Html::encode($category->name) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="<?= Url::to(['/site/about']) ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        О нас
                    </a>
                    <a href="<?= Url::to(['/site/contact']) ?>" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Контакты
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-1">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content -->
                <main class="flex-1 min-w-0">
                    <!-- Breadcrumbs -->
                    <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <nav class="mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm text-gray-500">
                            <li>
                                <a href="<?= Url::home() ?>" class="hover:text-gray-700">Главная</a>
                            </li>
                            <?php foreach ($this->params['breadcrumbs'] as $item): ?>
                            <li class="flex items-center">
                                <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <?php if (is_array($item) && isset($item['url'])): ?>
                                    <a href="<?= Url::to($item['url']) ?>" class="hover:text-gray-700"><?= Html::encode($item['label']) ?></a>
                                <?php elseif (is_array($item)): ?>
                                    <span class="text-gray-900"><?= Html::encode($item['label']) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-900"><?= Html::encode($item) ?></span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <?php endif; ?>
                    
                    <!-- Flash messages -->
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="mb-6 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800"><?= Yii::$app->session->getFlash('success') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="mb-6 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800"><?= Yii::$app->session->getFlash('error') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                </main>

                <!-- Sidebar -->
                <aside class="w-full lg:w-80 flex-shrink-0">
                    <!-- Categories Widget -->
                    <?php if (!empty($categories)): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Категории</h3>
                        <ul class="space-y-2">
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= Url::to(['/category/view', 'slug' => $category->slug]) ?>" 
                                   class="text-gray-600 hover:text-blue-600 text-sm flex items-center justify-between">
                                    <span><?= Html::encode($category->name) ?></span>
                                    <span class="text-gray-400 text-xs">(<?= $category->getPublications()->where(['status' => 'published'])->count() ?>)</span>
                                </a>
                                <?php if ($category->children): ?>
                                <ul class="ml-4 mt-2 space-y-1">
                                    <?php foreach ($category->children as $child): ?>
                                    <li>
                                        <a href="<?= Url::to(['/category/view', 'slug' => $child->slug]) ?>" 
                                           class="text-gray-500 hover:text-blue-600 text-sm flex items-center justify-between">
                                            <span><?= Html::encode($child->name) ?></span>
                                            <span class="text-gray-400 text-xs">(<?= $child->getPublications()->where(['status' => 'published'])->count() ?>)</span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Tags Cloud Widget -->
                    <?php if (!empty($tags)): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Теги</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($tags as $tag): ?>
                            <a href="<?= Url::to(['/tag/view', 'slug' => $tag->slug]) ?>" 
                               class="inline-block bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 text-sm px-3 py-1 rounded-full transition-colors">
                                <?= Html::encode($tag->name) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 text-sm">
                    &copy; <?= date('Y') ?> <?= Html::encode($siteName) ?>. Все права защищены.
                </div>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="<?= Url::to(['/site/about']) ?>" class="text-gray-500 hover:text-gray-700 text-sm">О нас</a>
                    <a href="<?= Url::to(['/site/contact']) ?>" class="text-gray-500 hover:text-gray-700 text-sm">Контакты</a>
                </div>
            </div>
        </div>
    </footer>
</div>

<?php $this->endBody() ?>
<script src="<?= Yii::getAlias('@web/js/search-autocomplete.js') ?>"></script>
</body>
</html>
<?php $this->endPage() ?>

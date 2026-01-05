<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AdminAsset;
use yii\helpers\Html;

AdminAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

$currentController = Yii::$app->controller->id;

$menuItems = [
    ['label' => 'Dashboard', 'url' => ['/admin/default/index'], 'controller' => 'default', 'icon' => 'home'],
    ['label' => 'Публикации', 'url' => ['/admin/publication/index'], 'controller' => 'publication', 'icon' => 'document'],
    ['label' => 'Категории', 'url' => ['/admin/category/index'], 'controller' => 'category', 'icon' => 'folder'],
    ['label' => 'Теги', 'url' => ['/admin/tag/index'], 'controller' => 'tag', 'icon' => 'tag'],
    ['label' => 'Страницы', 'url' => ['/admin/page/index'], 'controller' => 'page', 'icon' => 'page'],
    ['label' => 'Комментарии', 'url' => ['/admin/comment/index'], 'controller' => 'comment', 'icon' => 'chat'],
    ['label' => 'Пользователи', 'url' => ['/admin/user/index'], 'controller' => 'user', 'icon' => 'users'],
    ['label' => 'SEO', 'url' => ['/admin/seo/index'], 'controller' => 'seo', 'icon' => 'chart'],
    ['label' => 'Настройки', 'url' => ['/admin/setting/index'], 'controller' => 'setting', 'icon' => 'cog'],
];

$icons = [
    'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'folder' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
    'tag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
    'page' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>',
    'chat' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    'cog' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full bg-gray-100">
<head>
    <title><?= Html::encode($this->title) ?> - Admin Panel</title>
    <?php $this->head() ?>
</head>
<body class="h-full">
<?php $this->beginBody() ?>

<div class="min-h-full flex">
    <!-- Sidebar -->
    <div class="w-64 flex-shrink-0 bg-gray-800 min-h-screen">
        <div class="h-16 px-4"></div>
        <nav class="px-2 space-y-1">
            <?php foreach ($menuItems as $item): ?>
                <?php $isActive = $currentController === $item['controller']; ?>
                <?= Html::a(
                    '<svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' . $icons[$item['icon']] . '</svg>' . Html::encode($item['label']),
                    $item['url'],
                    [
                        'class' => 'group flex items-center px-3 py-2 text-sm font-medium rounded-md ' . 
                            ($isActive 
                                ? 'bg-gray-900 text-white' 
                                : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                    ]
                ) ?>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Main area -->
    <div class="flex-1 flex flex-col">
        <!-- Top header -->
        <nav class="bg-gray-800 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <span class="text-white text-xl font-bold">Admin</span>
            <div class="flex items-center">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <span class="text-gray-300 text-sm mr-4"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                    <?= Html::a('Выход', ['/admin/auth/logout'], [
                        'class' => 'rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700',
                        'data-method' => 'post'
                    ]) ?>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Main content -->
        <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
            <!-- Flash messages -->
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="mb-4 rounded-md bg-green-50 p-4">
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
                <div class="mb-4 rounded-md bg-red-50 p-4">
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
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

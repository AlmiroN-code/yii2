<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AdminAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
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

<div class="min-h-full">
    <!-- Navigation -->
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-white text-xl font-bold">Admin</span>
                    </div>
                    <div class="ml-10 flex items-baseline space-x-4">
                        <?= Html::a('Dashboard', ['/admin/default/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'default' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Публикации', ['/admin/publication/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'publication' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Категории', ['/admin/category/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'category' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Теги', ['/admin/tag/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'tag' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Настройки', ['/admin/setting/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'setting' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Пользователи', ['/admin/user/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'user' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                        <?= Html::a('Комментарии', ['/admin/comment/index'], [
                            'class' => 'rounded-md px-3 py-2 text-sm font-medium ' . 
                                (Yii::$app->controller->id === 'comment' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
                        ]) ?>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <span class="text-gray-300 text-sm mr-4"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                        <?= Html::a('Выход', ['/admin/auth/logout'], [
                            'class' => 'rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700',
                            'data-method' => 'post'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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
        </div>
    </main>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

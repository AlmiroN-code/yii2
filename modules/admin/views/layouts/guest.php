<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AdminAsset;
use yii\helpers\Html;

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

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

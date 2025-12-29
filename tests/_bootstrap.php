<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ .'/../vendor/autoload.php';

// Load DI container configuration
$containerConfig = require __DIR__ . '/../config/container.php';
if (isset($containerConfig['definitions'])) {
    foreach ($containerConfig['definitions'] as $interface => $implementation) {
        Yii::$container->set($interface, $implementation);
    }
}

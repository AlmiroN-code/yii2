<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$container = require __DIR__ . '/container.php';

// Регистрация зависимостей в DI контейнере
// Requirements: 3.1, 3.2
foreach ($container['definitions'] as $interface => $implementation) {
    Yii::$container->set($interface, $implementation);
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'app\components\RedirectHandler'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'gxZiMrPg6Y1pfksi8uQ2Z97BtECqj-I9',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'seo' => [
            'class' => 'app\components\SeoComponent',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Auth routes
                'login' => 'auth/login',
                'register' => 'auth/register',
                'logout' => 'auth/logout',
                'contact' => 'site/contact',
                'about' => 'site/about',
                
                // Profile routes
                'profile/<username:[\w-]+>' => 'profile/view',
                'profile/<username:[\w-]+>/edit' => 'profile/edit',
                'profile/<username:[\w-]+>/password' => 'profile/password',
                'profile/<username:[\w-]+>/favorites' => 'profile/favorites',
                'profile/<username:[\w-]+>/publications' => 'profile/publications',
                
                // API routes
                'api/favorite/toggle/<id:\d+>' => 'favorite/toggle',
                'api/search/autocomplete' => 'search/autocomplete',
                'search' => 'search/index',
                
                // SEO routes
                'sitemap.xml' => 'site/sitemap',
                'robots.txt' => 'site/robots',
                
                // Frontend publication routes
                'publications' => 'publication/index',
                'publication/create' => 'publication/create',
                'publication/my' => 'publication/my',
                'publication/<id:\d+>/edit' => 'publication/update',
                'publication/<id:\d+>/delete' => 'publication/delete',
                'publication/<slug:[\w-]+>' => 'publication/view',
                
                // Frontend category routes
                'category/<slug:[\w-]+>' => 'category/view',
                
                // Frontend tag routes
                'tags' => 'tag/index',
                'tag/<slug:[\w-]+>' => 'tag/view',
                
                // Frontend page routes
                'page/<slug:[\w-]+>' => 'page/view',
                
                // Admin module routes
                'admin' => 'admin/default/index',
                'admin/login' => 'admin/auth/login',
                'admin/logout' => 'admin/auth/logout',
                
                // Admin SEO routes
                'admin/seo' => 'admin/seo/index',
                'admin/seo/sitemap' => 'admin/seo/sitemap',
                'admin/seo/generate-sitemap' => 'admin/seo/generate-sitemap',
                'admin/seo/robots' => 'admin/seo/robots',
                'admin/seo/redirects' => 'admin/seo/redirects',
                'admin/seo/create-redirect' => 'admin/seo/create-redirect',
                'admin/seo/update-redirect/<id:\d+>' => 'admin/seo/update-redirect',
                'admin/seo/delete-redirect/<id:\d+>' => 'admin/seo/delete-redirect',
                'admin/seo/webmaster' => 'admin/seo/webmaster',
                
                'admin/<controller:[\w-]+>' => 'admin/<controller>/index',
                'admin/<controller:[\w-]+>/<action:[\w-]+>' => 'admin/<controller>/<action>',
                'admin/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => 'admin/<controller>/<action>',
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

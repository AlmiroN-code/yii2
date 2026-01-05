<?php

declare(strict_types=1);

namespace app\components;

use app\models\Redirect;
use yii\base\BootstrapInterface;
use yii\web\Application;

/**
 * Компонент для обработки редиректов при загрузке приложения.
 * Requirements: 8.6
 */
class RedirectHandler implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        if (!($app instanceof Application)) {
            return;
        }

        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            $this->handleRedirect($app);
        });
    }

    /**
     * Проверяет и выполняет редирект если найден.
     */
    private function handleRedirect(Application $app): void
    {
        $request = $app->request;
        $pathInfo = '/' . ltrim($request->pathInfo, '/');

        $redirect = Redirect::findBySourceUrl($pathInfo);
        if ($redirect === null) {
            return;
        }

        // Увеличиваем счётчик переходов
        $redirect->incrementHits();

        // Выполняем редирект
        $app->response->redirect($redirect->target_url, $redirect->type)->send();
        $app->end();
    }
}

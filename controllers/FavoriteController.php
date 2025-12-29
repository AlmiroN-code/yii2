<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Favorite;
use app\models\Publication;

/**
 * FavoriteController - API контроллер закладок.
 * Requirements: 3.1, 3.2
 */
class FavoriteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->statusCode = 401;
                    return ['success' => false, 'message' => 'Требуется авторизация', 'redirect' => '/login'];
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Toggles favorite status.
     * Requirements: 3.1, 3.2
     */
    public function actionToggle(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $publication = Publication::findOne($id);
        if (!$publication) {
            Yii::$app->response->statusCode = 404;
            return ['success' => false, 'message' => 'Публикация не найдена'];
        }

        $userId = Yii::$app->user->id;
        $isFavorite = Favorite::toggle($userId, $id);
        $count = Favorite::getCount($id);

        return [
            'success' => true,
            'isFavorite' => $isFavorite,
            'count' => $count,
            'message' => $isFavorite ? 'Добавлено в избранное' : 'Удалено из избранного',
        ];
    }
}

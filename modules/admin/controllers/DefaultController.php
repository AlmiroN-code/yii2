<?php

namespace app\modules\admin\controllers;

use app\models\Category;
use app\models\Publication;
use app\models\Tag;
use app\enums\PublicationStatus;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Default controller for admin module
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
                    return $this->redirect(['/admin/auth/login']);
                },
            ],
        ];
    }

    /**
     * Dashboard action.
     *
     * @return string
     */
    public function actionIndex()
    {
        $publicationsCount = Publication::find()->count();
        $publishedCount = Publication::find()->where(['status' => PublicationStatus::PUBLISHED->value])->count();
        $draftCount = Publication::find()->where(['status' => PublicationStatus::DRAFT->value])->count();
        $categoriesCount = Category::find()->count();
        $tagsCount = Tag::find()->count();

        return $this->render('index', [
            'publicationsCount' => $publicationsCount,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'categoriesCount' => $categoriesCount,
            'tagsCount' => $tagsCount,
        ]);
    }
}

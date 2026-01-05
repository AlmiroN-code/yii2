<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Page;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PageController - отображение статических страниц.
 */
class PageController extends Controller
{
    public function actionView(string $slug): string
    {
        $model = Page::findBySlug($slug);

        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        // SEO
        $seo = Yii::$app->seo;
        $seo->title = $model->meta_title ?: $model->title;
        $seo->description = $model->meta_description ?: mb_substr(strip_tags($model->content), 0, 160);
        $seo->canonicalUrl = \yii\helpers\Url::to(['/page/view', 'slug' => $model->slug], true);

        // Breadcrumbs
        $this->view->params['breadcrumbs'][] = $model->title;

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}

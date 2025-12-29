<?php

namespace app\controllers;

use app\models\Category;
use app\models\Publication;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController handles frontend category display.
 * Requirements: 4.3
 */
class CategoryController extends Controller
{
    /**
     * Displays publications filtered by category slug.
     * Requirements: 4.3
     *
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException if category not found
     */
    public function actionView(string $slug): string
    {
        $category = Category::find()
            ->where(['slug' => $slug])
            ->one();

        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Publication::findPublished()
                ->with(['category', 'tags'])
                ->andWhere(['category_id' => $category->id])
                ->orderBy(['published_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        // Breadcrumbs
        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forCategory($category);

        return $this->render('view', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }
}

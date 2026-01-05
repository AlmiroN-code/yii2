<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Publication;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CategoryController handles frontend category display.
 * Requirements: 4.3, 8.2, 8.7
 */
class CategoryController extends Controller
{
    /**
     * Displays publications filtered by category slug.
     * Requirements: 4.3, 8.2, 8.7
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

        // SEO: Meta tags
        $seo = Yii::$app->seo;
        $seo->title = $category->meta_title ?: $category->name;
        $seo->description = $category->meta_description ?: $category->description;
        $seo->canonicalUrl = \yii\helpers\Url::to(['/category/view', 'slug' => $category->slug], true);
        
        // SEO: Open Graph
        $seo->ogTags = [
            'og:title' => $category->meta_title ?: $category->name,
            'og:description' => $category->meta_description ?: $category->description,
            'og:type' => 'website',
            'og:url' => \yii\helpers\Url::to(['/category/view', 'slug' => $category->slug], true),
        ];

        // SEO: Schema.org CollectionPage
        /** @var \app\services\SeoServiceInterface $seoService */
        $seoService = Yii::$container->get(\app\services\SeoServiceInterface::class);
        $seo->schemaOrg = $seoService->getCollectionSchema($category);

        return $this->render('view', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }
}

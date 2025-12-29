<?php

namespace app\controllers;

use app\models\Publication;
use app\models\Tag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TagController handles frontend tag display.
 * Requirements: 4.4
 */
class TagController extends Controller
{
    /**
     * Displays all tags.
     */
    public function actionIndex(): string
    {
        $tags = Tag::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $this->view->params['breadcrumbs'][] = 'Теги';

        return $this->render('index', [
            'tags' => $tags,
        ]);
    }

    /**
     * Displays publications filtered by tag slug.
     * Requirements: 4.4
     *
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException if tag not found
     */
    public function actionView(string $slug): string
    {
        $tag = Tag::find()
            ->where(['slug' => $slug])
            ->one();

        if ($tag === null) {
            throw new NotFoundHttpException('Тег не найден.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Publication::findPublished()
                ->with(['category', 'tags'])
                ->innerJoinWith('publicationTags')
                ->andWhere(['{{%publication_tag}}.tag_id' => $tag->id])
                ->orderBy(['published_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        // Breadcrumbs
        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forTag($tag);

        return $this->render('view', [
            'tag' => $tag,
            'dataProvider' => $dataProvider,
        ]);
    }
}

<?php

namespace app\services;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Publication;
use app\models\Category;
use app\models\Tag;

/**
 * SearchService - сервис поиска.
 * Requirements: 7.2, 7.6
 */
class SearchService
{
    /**
     * Autocomplete search.
     * Requirements: 7.2
     */
    public function autocomplete(string $query, int $limit = 10): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = [];

        // Search publications
        $publications = Publication::find()
            ->where(['status' => Publication::STATUS_PUBLISHED])
            ->andWhere(['like', 'title', $query])
            ->limit(5)
            ->all();

        foreach ($publications as $pub) {
            $results[] = [
                'type' => 'publication',
                'title' => $pub->title,
                'url' => Yii::$app->urlManager->createUrl(['publication/view', 'slug' => $pub->slug]),
                'preview' => mb_substr($pub->excerpt ?? '', 0, 100),
            ];
        }

        // Search categories
        $categories = Category::find()
            ->where(['like', 'name', $query])
            ->limit(3)
            ->all();

        foreach ($categories as $cat) {
            $results[] = [
                'type' => 'category',
                'title' => $cat->name,
                'url' => Yii::$app->urlManager->createUrl(['category/view', 'slug' => $cat->slug]),
                'preview' => $cat->description ?? '',
            ];
        }

        // Search tags
        $tags = Tag::find()
            ->where(['like', 'name', $query])
            ->limit(3)
            ->all();

        foreach ($tags as $tag) {
            $results[] = [
                'type' => 'tag',
                'title' => $tag->name,
                'url' => Yii::$app->urlManager->createUrl(['tag/view', 'slug' => $tag->slug]),
                'preview' => '',
            ];
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * Full search with pagination.
     */
    public function search(string $query, int $pageSize = 10): ActiveDataProvider
    {
        $query = trim($query);

        return new ActiveDataProvider([
            'query' => Publication::find()
                ->where(['status' => Publication::STATUS_PUBLISHED])
                ->andWhere([
                    'or',
                    ['like', 'title', $query],
                    ['like', 'content', $query],
                    ['like', 'excerpt', $query],
                ])
                ->orderBy(['published_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
    }
}

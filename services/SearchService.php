<?php

declare(strict_types=1);

namespace app\services;

use Yii;
use yii\data\ActiveDataProvider;
use app\repositories\PublicationRepositoryInterface;
use app\repositories\CategoryRepositoryInterface;
use app\repositories\TagRepositoryInterface;

/**
 * SearchService - сервис поиска.
 * Requirements: 2.1, 2.3, 2.4, 3.4
 */
class SearchService
{
    private $publicationRepository;
    private $categoryRepository;
    private $tagRepository;

    public function __construct(
        PublicationRepositoryInterface $publicationRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->publicationRepository = $publicationRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Autocomplete search.
     * Requirements: 2.1, 2.3, 2.4
     */
    public function autocomplete(string $query, int $limit = 10): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = [];

        // Search publications using repository
        $publications = $this->publicationRepository->findPublished()
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


        // Search categories using repository
        $categories = $this->categoryRepository->findAll();
        $filteredCategories = array_filter($categories, function ($cat) use ($query) {
            return mb_stripos($cat->name, $query) !== false;
        });
        $filteredCategories = \array_slice($filteredCategories, 0, 3);

        foreach ($filteredCategories as $cat) {
            $results[] = [
                'type' => 'category',
                'title' => $cat->name,
                'url' => Yii::$app->urlManager->createUrl(['category/view', 'slug' => $cat->slug]),
                'preview' => $cat->description ?? '',
            ];
        }

        // Search tags using repository
        $tags = $this->tagRepository->findAll();
        $filteredTags = array_filter($tags, function ($tag) use ($query) {
            return mb_stripos($tag->name, $query) !== false;
        });
        $filteredTags = \array_slice($filteredTags, 0, 3);

        foreach ($filteredTags as $tag) {
            $results[] = [
                'type' => 'tag',
                'title' => $tag->name,
                'url' => Yii::$app->urlManager->createUrl(['tag/view', 'slug' => $tag->slug]),
                'preview' => '',
            ];
        }

        return \array_slice($results, 0, $limit);
    }

    /**
     * Full search with pagination.
     * Requirements: 2.1
     */
    public function search(string $query, int $pageSize = 10): ActiveDataProvider
    {
        $query = trim($query);

        return new ActiveDataProvider([
            'query' => $this->publicationRepository->findPublished()
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

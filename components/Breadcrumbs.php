<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Publication;
use app\models\Category;
use app\models\Tag;
use app\models\User;

/**
 * Breadcrumbs component.
 * Requirements: 6.1-6.5
 */
class Breadcrumbs extends Component
{
    /**
     * Builds breadcrumbs array for Yii2 widget.
     */
    public static function build(array $items): array
    {
        return $items;
    }

    /**
     * Builds breadcrumbs for publication page.
     * Requirements: 6.2
     */
    public static function forPublication(Publication $publication): array
    {
        $items = [];

        // Category hierarchy
        if ($publication->category) {
            $items = array_merge($items, self::getCategoryChain($publication->category));
        }

        // Current publication (not a link)
        $items[] = $publication->title;

        return $items;
    }

    /**
     * Builds breadcrumbs for category page.
     * Requirements: 6.3
     */
    public static function forCategory(Category $category): array
    {
        $items = self::getCategoryChain($category);
        
        // Remove last item's link (current page)
        if (!empty($items)) {
            $lastKey = array_key_last($items);
            $items[$lastKey] = $items[$lastKey]['label'];
        }

        return $items;
    }

    /**
     * Builds breadcrumbs for tag page.
     */
    public static function forTag(Tag $tag): array
    {
        return [
            ['label' => 'Теги', 'url' => ['/tag/index']],
            $tag->name,
        ];
    }

    /**
     * Builds breadcrumbs for profile page.
     * Requirements: 6.4
     */
    public static function forProfile(User $user, ?string $section = null): array
    {
        $items = [
            ['label' => 'Профили', 'url' => '#'],
            ['label' => $user->getDisplayName(), 'url' => ['/profile/view', 'username' => $user->username]],
        ];

        if ($section) {
            $items[] = $section;
        } else {
            // Remove link from last item
            $items[count($items) - 1] = $user->getDisplayName();
        }

        return $items;
    }

    /**
     * Builds breadcrumbs for search page.
     */
    public static function forSearch(?string $query = null): array
    {
        $items = ['Поиск'];
        
        if ($query) {
            $items = [
                ['label' => 'Поиск', 'url' => ['/search/index']],
                'Результаты: ' . $query,
            ];
        }

        return $items;
    }

    /**
     * Gets category chain from root to current.
     */
    private static function getCategoryChain(Category $category): array
    {
        $chain = [];
        $current = $category;

        while ($current) {
            array_unshift($chain, [
                'label' => $current->name,
                'url' => ['/category/view', 'slug' => $current->slug],
            ]);
            $current = $current->parent;
        }

        return $chain;
    }
}

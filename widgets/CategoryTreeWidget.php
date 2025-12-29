<?php

namespace app\widgets;

use app\models\Category;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * CategoryTreeWidget renders a hierarchical category list.
 * Requirements: 2.4
 *
 * Usage:
 * ```php
 * <?= CategoryTreeWidget::widget() ?>
 * <?= CategoryTreeWidget::widget(['showCount' => true]) ?>
 * ```
 */
class CategoryTreeWidget extends Widget
{
    /**
     * @var bool whether to show publication count for each category
     */
    public bool $showCount = true;

    /**
     * @var string|null CSS class for the root ul element
     */
    public ?string $listClass = 'space-y-2';

    /**
     * @var string|null CSS class for nested ul elements
     */
    public ?string $nestedListClass = 'ml-4 mt-2 space-y-1';

    /**
     * @var string|null CSS class for li elements
     */
    public ?string $itemClass = null;

    /**
     * @var string|null CSS class for links
     */
    public ?string $linkClass = 'text-gray-700 hover:text-blue-600 transition-colors';

    /**
     * @var string|null CSS class for count badge
     */
    public ?string $countClass = 'text-sm text-gray-500 ml-1';

    /**
     * @var Category[]|null cached categories
     */
    private ?array $_categories = null;

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        $rootCategories = $this->getRootCategories();
        
        if (empty($rootCategories)) {
            return '';
        }

        return $this->renderTree($rootCategories, true);
    }

    /**
     * Gets root categories (categories without parent).
     *
     * @return Category[]
     */
    protected function getRootCategories(): array
    {
        return Category::find()
            ->where(['parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Renders the category tree recursively.
     *
     * @param Category[] $categories
     * @param bool $isRoot
     * @return string
     */
    protected function renderTree(array $categories, bool $isRoot = false): string
    {
        $items = [];
        
        foreach ($categories as $category) {
            $items[] = $this->renderItem($category);
        }

        $class = $isRoot ? $this->listClass : $this->nestedListClass;
        
        return Html::tag('ul', implode("\n", $items), ['class' => $class]);
    }

    /**
     * Renders a single category item with its children.
     *
     * @param Category $category
     * @return string
     */
    protected function renderItem(Category $category): string
    {
        $content = $this->renderLink($category);
        
        // Get children
        $children = $category->children;
        if (!empty($children)) {
            $content .= $this->renderTree($children);
        }

        return Html::tag('li', $content, ['class' => $this->itemClass]);
    }

    /**
     * Renders the category link.
     *
     * @param Category $category
     * @return string
     */
    protected function renderLink(Category $category): string
    {
        $label = Html::encode($category->name);
        
        if ($this->showCount) {
            $count = $this->getPublicationCount($category);
            $label .= Html::tag('span', "({$count})", ['class' => $this->countClass]);
        }

        return Html::a($label, ['category/view', 'slug' => $category->slug], [
            'class' => $this->linkClass,
        ]);
    }

    /**
     * Gets the publication count for a category.
     *
     * @param Category $category
     * @return int
     */
    protected function getPublicationCount(Category $category): int
    {
        return (int) $category->getPublications()
            ->andWhere(['status' => 'published'])
            ->count();
    }
}

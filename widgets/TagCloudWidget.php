<?php

namespace app\widgets;

use app\models\Tag;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * TagCloudWidget renders a tag cloud with usage counts.
 * Requirements: 3.4
 *
 * Usage:
 * ```php
 * <?= TagCloudWidget::widget() ?>
 * <?= TagCloudWidget::widget(['maxTags' => 20, 'showCount' => false]) ?>
 * ```
 */
class TagCloudWidget extends Widget
{
    /**
     * @var int maximum number of tags to display (0 = unlimited)
     */
    public int $maxTags = 0;

    /**
     * @var bool whether to show publication count for each tag
     */
    public bool $showCount = true;

    /**
     * @var string|null CSS class for the container div
     */
    public ?string $containerClass = 'flex flex-wrap gap-2';

    /**
     * @var string base CSS class for tag links
     */
    public string $tagBaseClass = 'inline-block px-3 py-1 rounded-full transition-colors';

    /**
     * @var array size classes based on tag popularity (small to large)
     */
    public array $sizeClasses = [
        'text-xs bg-gray-100 text-gray-600 hover:bg-gray-200',
        'text-sm bg-gray-200 text-gray-700 hover:bg-gray-300',
        'text-base bg-blue-100 text-blue-700 hover:bg-blue-200',
        'text-lg bg-blue-200 text-blue-800 hover:bg-blue-300',
        'text-xl bg-blue-300 text-blue-900 hover:bg-blue-400',
    ];

    /**
     * @var string|null CSS class for count badge
     */
    public ?string $countClass = 'text-xs opacity-75 ml-1';

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        $tags = $this->getTagsWithCounts();
        
        if (empty($tags)) {
            return '';
        }

        $items = [];
        $counts = array_column($tags, 'count');
        $minCount = min($counts);
        $maxCount = max($counts);

        foreach ($tags as $tag) {
            $items[] = $this->renderTag($tag, $minCount, $maxCount);
        }

        return Html::tag('div', implode("\n", $items), ['class' => $this->containerClass]);
    }

    /**
     * Gets tags with their publication counts.
     *
     * @return array Array of ['tag' => Tag, 'count' => int]
     */
    protected function getTagsWithCounts(): array
    {
        $query = Tag::find()
            ->alias('t')
            ->select(['t.*', 'COUNT(pt.publication_id) as publication_count'])
            ->leftJoin('{{%publication_tag}} pt', 'pt.tag_id = t.id')
            ->leftJoin('{{%publication}} p', 'p.id = pt.publication_id AND p.status = :status', [':status' => 'published'])
            ->groupBy('t.id')
            ->having('COUNT(pt.publication_id) > 0')
            ->orderBy(['publication_count' => SORT_DESC]);

        if ($this->maxTags > 0) {
            $query->limit($this->maxTags);
        }

        $result = [];
        foreach ($query->all() as $tag) {
            $result[] = [
                'tag' => $tag,
                'count' => (int) $tag->publication_count,
            ];
        }

        return $result;
    }

    /**
     * Renders a single tag.
     *
     * @param array $tagData ['tag' => Tag, 'count' => int]
     * @param int $minCount
     * @param int $maxCount
     * @return string
     */
    protected function renderTag(array $tagData, int $minCount, int $maxCount): string
    {
        $tag = $tagData['tag'];
        $count = $tagData['count'];

        $sizeClass = $this->getSizeClass($count, $minCount, $maxCount);
        $class = trim($this->tagBaseClass . ' ' . $sizeClass);

        $label = Html::encode($tag->name);
        
        if ($this->showCount) {
            $label .= Html::tag('span', "({$count})", ['class' => $this->countClass]);
        }

        return Html::a($label, ['tag/view', 'slug' => $tag->slug], ['class' => $class]);
    }

    /**
     * Gets the size class based on tag count relative to min/max.
     *
     * @param int $count
     * @param int $minCount
     * @param int $maxCount
     * @return string
     */
    protected function getSizeClass(int $count, int $minCount, int $maxCount): string
    {
        $numClasses = count($this->sizeClasses);
        
        if ($maxCount === $minCount) {
            // All tags have the same count, use middle size
            return $this->sizeClasses[(int) floor($numClasses / 2)];
        }

        // Calculate which size class to use based on count
        $ratio = ($count - $minCount) / ($maxCount - $minCount);
        $index = (int) floor($ratio * ($numClasses - 1));
        
        return $this->sizeClasses[$index];
    }
}

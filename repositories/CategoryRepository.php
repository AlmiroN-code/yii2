<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Category;
use yii\db\ActiveRecord;

/**
 * Реализация репозитория категорий.
 * Requirements: 2.3, 2.5
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Category
    {
        return Category::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @return Category[]
     */
    public function findAll(): array
    {
        return Category::find()->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])->all();
    }

    /**
     * {@inheritdoc}
     */
    public function save(ActiveRecord $model): bool
    {
        return $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ActiveRecord $model): bool
    {
        return $model->delete() !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::findOne(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findRoots(): array
    {
        return Category::find()
            ->where(['parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findChildren(int $parentId): array
    {
        return Category::find()
            ->where(['parent_id' => $parentId])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])
            ->all();
    }
}

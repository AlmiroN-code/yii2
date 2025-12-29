<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Tag;
use yii\db\ActiveRecord;

/**
 * Реализация репозитория тегов.
 * Requirements: 2.4, 2.5
 */
class TagRepository implements TagRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Tag
    {
        return Tag::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @return Tag[]
     */
    public function findAll(): array
    {
        return Tag::find()->orderBy(['name' => SORT_ASC])->all();
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
    public function findBySlug(string $slug): ?Tag
    {
        return Tag::findOne(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findPopular(int $limit = 20): array
    {
        return Tag::find()
            ->select(['{{%tag}}.*', 'COUNT({{%publication_tag}}.publication_id) as usage_count'])
            ->leftJoin('{{%publication_tag}}', '{{%tag}}.id = {{%publication_tag}}.tag_id')
            ->groupBy('{{%tag}}.id')
            ->orderBy(['usage_count' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}

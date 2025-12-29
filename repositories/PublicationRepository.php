<?php

declare(strict_types=1);

namespace app\repositories;

use app\enums\PublicationStatus;
use app\models\Publication;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Реализация репозитория публикаций.
 * Requirements: 2.1, 2.5
 */
class PublicationRepository implements PublicationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Publication
    {
        return Publication::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @return Publication[]
     */
    public function findAll(): array
    {
        return Publication::find()->all();
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
    public function findBySlug(string $slug): ?Publication
    {
        return Publication::findOne(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findPublished(): ActiveQuery
    {
        return Publication::find()->where(['status' => PublicationStatus::PUBLISHED->value]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByAuthor(int $authorId): ActiveQuery
    {
        return Publication::find()->where(['author_id' => $authorId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCategory(int $categoryId): ActiveQuery
    {
        return Publication::find()->where(['category_id' => $categoryId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByTag(int $tagId): ActiveQuery
    {
        return Publication::find()
            ->innerJoin('{{%publication_tag}}', '{{%publication}}.id = {{%publication_tag}}.publication_id')
            ->where(['{{%publication_tag}}.tag_id' => $tagId]);
    }
}

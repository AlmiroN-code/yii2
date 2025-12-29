<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Publication;
use yii\db\ActiveQuery;

/**
 * Интерфейс репозитория публикаций.
 * Requirements: 2.1, 2.5
 */
interface PublicationRepositoryInterface extends RepositoryInterface
{
    /**
     * Находит публикацию по slug.
     */
    public function findBySlug(string $slug): ?Publication;

    /**
     * Возвращает запрос для опубликованных публикаций.
     */
    public function findPublished(): ActiveQuery;

    /**
     * Возвращает запрос для публикаций автора.
     */
    public function findByAuthor(int $authorId): ActiveQuery;

    /**
     * Возвращает запрос для публикаций категории.
     */
    public function findByCategory(int $categoryId): ActiveQuery;

    /**
     * Возвращает запрос для публикаций с тегом.
     */
    public function findByTag(int $tagId): ActiveQuery;
}

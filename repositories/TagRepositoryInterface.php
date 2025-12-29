<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Tag;

/**
 * Интерфейс репозитория тегов.
 * Requirements: 2.4, 2.5
 */
interface TagRepositoryInterface extends RepositoryInterface
{
    /**
     * Находит тег по slug.
     */
    public function findBySlug(string $slug): ?Tag;

    /**
     * Возвращает популярные теги.
     *
     * @return Tag[]
     */
    public function findPopular(int $limit = 20): array;
}

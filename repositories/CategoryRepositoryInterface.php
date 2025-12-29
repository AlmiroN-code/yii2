<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Category;

/**
 * Интерфейс репозитория категорий.
 * Requirements: 2.3, 2.5
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * Находит категорию по slug.
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Возвращает корневые категории (без родителя).
     *
     * @return Category[]
     */
    public function findRoots(): array;

    /**
     * Возвращает дочерние категории.
     *
     * @return Category[]
     */
    public function findChildren(int $parentId): array;
}

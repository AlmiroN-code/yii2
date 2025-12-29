<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\ActiveRecord;

/**
 * Базовый интерфейс репозитория.
 * Requirements: 2.5
 */
interface RepositoryInterface
{
    /**
     * Находит запись по ID.
     */
    public function findById(int $id): ?ActiveRecord;

    /**
     * Возвращает все записи.
     *
     * @return ActiveRecord[]
     */
    public function findAll(): array;

    /**
     * Сохраняет модель.
     */
    public function save(ActiveRecord $model): bool;

    /**
     * Удаляет модель.
     */
    public function delete(ActiveRecord $model): bool;
}

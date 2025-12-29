<?php

declare(strict_types=1);

namespace app\services;

use app\models\Publication;

/**
 * Интерфейс сервиса публикаций.
 * Requirements: 1.2, 3.1, 3.2, 3.3, 3.4
 */
interface PublicationServiceInterface
{
    /**
     * Создаёт новую публикацию.
     *
     * @param array $data Данные публикации
     * @param int $authorId ID автора
     * @return Publication|null Созданная публикация или null при ошибке
     */
    public function create(array $data, int $authorId): ?Publication;

    /**
     * Обновляет существующую публикацию.
     *
     * @param Publication $publication Публикация для обновления
     * @param array $data Новые данные
     * @return bool Успешность операции
     */
    public function update(Publication $publication, array $data): bool;

    /**
     * Удаляет публикацию.
     *
     * @param Publication $publication Публикация для удаления
     * @return bool Успешность операции
     */
    public function delete(Publication $publication): bool;

    /**
     * Публикует публикацию (меняет статус на published).
     *
     * @param Publication $publication Публикация для публикации
     * @return bool Успешность операции
     */
    public function publish(Publication $publication): bool;

    /**
     * Архивирует публикацию (меняет статус на archived).
     *
     * @param Publication $publication Публикация для архивации
     * @return bool Успешность операции
     */
    public function archive(Publication $publication): bool;

    /**
     * Увеличивает счётчик просмотров публикации.
     *
     * @param Publication $publication Публикация
     */
    public function incrementViews(Publication $publication): void;
}

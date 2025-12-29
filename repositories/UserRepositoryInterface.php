<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\User;
use yii\db\ActiveQuery;

/**
 * Интерфейс репозитория пользователей.
 * Requirements: 2.2, 2.5
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Находит пользователя по username.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Находит пользователя по email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Находит пользователя по username или email.
     */
    public function findByUsernameOrEmail(string $identity): ?User;

    /**
     * Возвращает запрос для активных пользователей.
     */
    public function findActive(): ActiveQuery;
}

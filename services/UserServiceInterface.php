<?php

declare(strict_types=1);

namespace app\services;

use app\models\User;

/**
 * Интерфейс сервиса пользователей.
 * Requirements: 1.3, 3.1, 3.2, 3.4
 */
interface UserServiceInterface
{
    /**
     * Регистрирует нового пользователя.
     *
     * @param array $data Данные пользователя (username, email, password)
     * @return User|null Созданный пользователь или null при ошибке
     */
    public function register(array $data): ?User;

    /**
     * Обновляет профиль пользователя.
     *
     * @param User $user Пользователь
     * @param array $data Новые данные профиля
     * @return bool Успешность операции
     */
    public function updateProfile(User $user, array $data): bool;

    /**
     * Изменяет пароль пользователя.
     *
     * @param User $user Пользователь
     * @param string $newPassword Новый пароль
     * @return bool Успешность операции
     */
    public function changePassword(User $user, string $newPassword): bool;

    /**
     * Изменяет роль пользователя.
     *
     * @param User $user Пользователь
     * @param string $role Новая роль
     * @return bool Успешность операции
     */
    public function changeRole(User $user, string $role): bool;

    /**
     * Блокирует пользователя.
     *
     * @param User $user Пользователь для блокировки
     * @return bool Успешность операции
     */
    public function ban(User $user): bool;

    /**
     * Активирует пользователя.
     *
     * @param User $user Пользователь для активации
     * @return bool Успешность операции
     */
    public function activate(User $user): bool;
}

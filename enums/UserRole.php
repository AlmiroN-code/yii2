<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Enum для ролей пользователя.
 * Requirements: 4.2, 4.3
 */
enum UserRole: string
{
    case USER = 'user';
    case AUTHOR = 'author';
    case MODERATOR = 'moderator';
    case ADMIN = 'admin';

    /**
     * Возвращает человекочитаемую метку для роли.
     */
    public function label(): string
    {
        return match ($this) {
            self::USER => 'Пользователь',
            self::AUTHOR => 'Автор',
            self::MODERATOR => 'Модератор',
            self::ADMIN => 'Администратор',
        };
    }

    /**
     * Возвращает массив всех меток ролей.
     * 
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn(self $case) => $case->label(), self::cases())
        );
    }

    /**
     * Проверяет, может ли пользователь с данной ролью создавать публикации.
     */
    public function canCreatePublication(): bool
    {
        return match ($this) {
            self::AUTHOR, self::MODERATOR, self::ADMIN => true,
            default => false,
        };
    }

    /**
     * Проверяет, может ли пользователь с данной ролью модерировать контент.
     */
    public function canModerate(): bool
    {
        return match ($this) {
            self::MODERATOR, self::ADMIN => true,
            default => false,
        };
    }
}

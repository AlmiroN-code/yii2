<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Enum для статусов пользователя.
 * Requirements: 4.1, 4.3
 */
enum UserStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case BANNED = 2;

    /**
     * Возвращает человекочитаемую метку для статуса.
     */
    public function label(): string
    {
        return match ($this) {
            self::INACTIVE => 'Неактивен',
            self::ACTIVE => 'Активен',
            self::BANNED => 'Заблокирован',
        };
    }

    /**
     * Возвращает массив всех меток статусов.
     * 
     * @return array<int, string>
     */
    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn(self $case) => $case->label(), self::cases())
        );
    }
}

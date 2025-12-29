<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Enum для статусов публикации.
 * Requirements: 4.1, 4.3
 */
enum PublicationStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    /**
     * Возвращает человекочитаемую метку для статуса.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Черновик',
            self::PUBLISHED => 'Опубликовано',
            self::ARCHIVED => 'В архиве',
        };
    }

    /**
     * Возвращает массив всех меток статусов.
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
}

<?php

namespace app\services;

/**
 * Interface for slug generation service.
 * Requirements: 1.5, 7.2
 */
interface SlugServiceInterface
{
    /**
     * Generates a unique slug for the given title.
     *
     * @param string $title The title to generate slug from
     * @param string $table The database table name to check uniqueness against
     * @param int|null $excludeId ID to exclude from uniqueness check (for updates)
     * @return string The generated unique slug
     */
    public function generate(string $title, string $table, ?int $excludeId = null): string;
}

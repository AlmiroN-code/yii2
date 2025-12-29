<?php

namespace app\services;

use yii\db\Query;

/**
 * Service for generating unique SEO-friendly slugs.
 * Requirements: 1.5, 7.2
 */
class SlugService implements SlugServiceInterface
{
    /**
     * Cyrillic to Latin transliteration map.
     */
    private const TRANSLITERATION_MAP = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    ];

    /**
     * Generates a unique slug for the given title.
     *
     * @param string $title The title to generate slug from
     * @param string $table The database table name to check uniqueness against
     * @param int|null $excludeId ID to exclude from uniqueness check (for updates)
     * @return string The generated unique slug
     */
    public function generate(string $title, string $table, ?int $excludeId = null): string
    {
        $slug = $this->createSlug($title);
        
        if (empty($slug)) {
            $slug = 'item';
        }

        return $this->ensureUnique($slug, $table, $excludeId);
    }

    /**
     * Creates a slug from the given string (without uniqueness check).
     *
     * @param string $string The string to convert to slug
     * @return string The generated slug
     */
    public function createSlug(string $string): string
    {
        // Transliterate Cyrillic to Latin
        $slug = $this->transliterate($string);
        
        // Convert to lowercase
        $slug = mb_strtolower($slug, 'UTF-8');
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');
        
        // Collapse multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        return $slug;
    }

    /**
     * Transliterates Cyrillic characters to Latin.
     *
     * @param string $string The string to transliterate
     * @return string The transliterated string
     */
    public function transliterate(string $string): string
    {
        return strtr($string, self::TRANSLITERATION_MAP);
    }

    /**
     * Ensures the slug is unique in the given table.
     *
     * @param string $slug The base slug
     * @param string $table The database table name
     * @param int|null $excludeId ID to exclude from check
     * @return string The unique slug
     */
    protected function ensureUnique(string $slug, string $table, ?int $excludeId = null): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $table, $excludeId)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Checks if a slug exists in the given table.
     *
     * @param string $slug The slug to check
     * @param string $table The database table name
     * @param int|null $excludeId ID to exclude from check
     * @return bool True if slug exists
     */
    protected function slugExists(string $slug, string $table, ?int $excludeId = null): bool
    {
        $query = (new Query())
            ->from($table)
            ->where(['slug' => $slug]);

        if ($excludeId !== null) {
            $query->andWhere(['!=', 'id', $excludeId]);
        }

        return $query->exists();
    }
}

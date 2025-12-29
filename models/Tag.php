<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Tag model for publication tagging.
 * Requirements: 3.1
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 *
 * @property Publication[] $publications
 * @property PublicationTag[] $publicationTags
 */
class Tag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['slug'], 'string', 'max' => 100],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'slug' => 'URL-адрес',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Generate slug from name if empty
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($this->name);
        }

        return true;
    }

    /**
     * Generates a unique slug from the given string.
     */
    protected function generateSlug(string $string): string
    {
        // Transliterate Cyrillic to Latin
        $slug = $this->transliterate($string);
        
        // Convert to lowercase and replace spaces/special chars with hyphens
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Transliterates Cyrillic characters to Latin.
     */
    protected function transliterate(string $string): string
    {
        $converter = [
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

        return strtr($string, $converter);
    }

    /**
     * Checks if a slug already exists in the database.
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::find()->where(['slug' => $slug]);
        
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'id', $this->id]);
        }

        return $query->exists();
    }

    /**
     * Gets publications with this tag via junction table.
     */
    public function getPublications(): ActiveQuery
    {
        return $this->hasMany(Publication::class, ['id' => 'publication_id'])
            ->viaTable('{{%publication_tag}}', ['tag_id' => 'id']);
    }

    /**
     * Gets publication tag junction records.
     */
    public function getPublicationTags(): ActiveQuery
    {
        return $this->hasMany(PublicationTag::class, ['tag_id' => 'id']);
    }
}

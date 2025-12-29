<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Publication model for blog posts/articles.
 * Requirements: 1.1, 1.2
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $author_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property string|null $featured_image
 * @property string $status
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $views
 * @property string|null $published_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category|null $category
 * @property User|null $author
 * @property Tag[] $tags
 * @property PublicationTag[] $publicationTags
 */
class Publication extends ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    /**
     * @var array Tag IDs for form handling
     */
    public $tagIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%publication}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'content'], 'required'],
            [['title', 'meta_title'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['excerpt', 'content', 'meta_description'], 'string'],
            [['featured_image'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['category_id', 'author_id', 'views'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['views'], 'default', 'value' => 0],
            [['tagIds'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'category_id' => 'Категория',
            'author_id' => 'Автор',
            'title' => 'Заголовок',
            'slug' => 'URL-адрес',
            'excerpt' => 'Краткое описание',
            'content' => 'Содержимое',
            'featured_image' => 'Изображение',
            'status' => 'Статус',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'views' => 'Просмотры',
            'published_at' => 'Дата публикации',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'tagIds' => 'Теги',
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

        // Generate slug from title if empty
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug($this->title);
        }

        // Set published_at when status changes to published
        if ($this->status === self::STATUS_PUBLISHED && empty($this->published_at)) {
            $this->published_at = date('Y-m-d H:i:s');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        // Save tag assignments
        if (!empty($this->tagIds) || $this->tagIds === []) {
            $this->saveTags();
        }
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
     * Gets the category.
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets tags via junction table.
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%publication_tag}}', ['publication_id' => 'id']);
    }

    /**
     * Gets publication tag junction records.
     */
    public function getPublicationTags(): ActiveQuery
    {
        return $this->hasMany(PublicationTag::class, ['publication_id' => 'id']);
    }

    /**
     * Gets favorites for this publication.
     * Requirements: 3.5
     */
    public function getFavorites(): ActiveQuery
    {
        return $this->hasMany(Favorite::class, ['publication_id' => 'id']);
    }

    /**
     * Gets favorites count.
     * Requirements: 3.5
     */
    public function getFavoritesCount(): int
    {
        return (int)$this->getFavorites()->count();
    }

    /**
     * Gets comments for this publication.
     */
    public function getComments(): ActiveQuery
    {
        return $this->hasMany(Comment::class, ['publication_id' => 'id']);
    }

    /**
     * Gets approved comments.
     */
    public function getApprovedComments(): ActiveQuery
    {
        return $this->getComments()->where(['status' => Comment::STATUS_APPROVED]);
    }

    /**
     * Gets tag IDs for form handling.
     */
    public function getTagIds(): array
    {
        if (!empty($this->tagIds)) {
            return $this->tagIds;
        }
        return $this->getTags()->select('id')->column();
    }

    /**
     * Sets tag IDs for form handling.
     */
    public function setTagIds(array $ids): void
    {
        $this->tagIds = array_filter($ids, function ($id) {
            return !empty($id);
        });
    }

    /**
     * Saves tag assignments to junction table.
     */
    protected function saveTags(): void
    {
        // Delete existing tag assignments
        PublicationTag::deleteAll(['publication_id' => $this->id]);

        // Insert new tag assignments
        foreach ($this->tagIds as $tagId) {
            $publicationTag = new PublicationTag();
            $publicationTag->publication_id = $this->id;
            $publicationTag->tag_id = (int)$tagId;
            $publicationTag->save(false);
        }
    }

    /**
     * Returns published publications only.
     */
    public static function findPublished(): ActiveQuery
    {
        return static::find()->where(['status' => self::STATUS_PUBLISHED]);
    }

    /**
     * Returns status labels.
     */
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_PUBLISHED => 'Опубликовано',
        ];
    }

    /**
     * Returns status label for current publication.
     */
    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }
}

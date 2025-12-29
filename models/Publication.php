<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\enums\PublicationStatus;
use app\services\SlugServiceInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Publication model for blog posts/articles.
 * Requirements: 1.1, 1.2, 1.4, 4.1, 5.1, 5.3, 5.4
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
    /**
     * @var array Tag IDs for form handling
     */
    public array $tagIds = [];

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
            [['status'], 'in', 'range' => array_column(PublicationStatus::cases(), 'value')],
            [['status'], 'default', 'value' => PublicationStatus::DRAFT->value],
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

        // Generate slug from title if empty using SlugService via DI
        if (empty($this->slug)) {
            /** @var SlugServiceInterface $slugService */
            $slugService = Yii::$container->get(SlugServiceInterface::class);
            $this->slug = $slugService->generate(
                $this->title,
                self::tableName(),
                $this->isNewRecord ? null : $this->id
            );
        }

        // Set published_at when status changes to published
        if ($this->getPublicationStatus() === PublicationStatus::PUBLISHED && empty($this->published_at)) {
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
     * Gets the publication status as enum.
     */
    public function getPublicationStatus(): PublicationStatus
    {
        return PublicationStatus::tryFrom($this->status) ?? PublicationStatus::DRAFT;
    }

    /**
     * Sets the publication status from enum.
     */
    public function setPublicationStatus(PublicationStatus $status): void
    {
        $this->status = $status->value;
    }

    /**
     * Gets the category.
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets the author.
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
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
     * 
     * @return int[]
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
     * 
     * @param array $ids
     */
    public function setTagIds(array $ids): void
    {
        $this->tagIds = array_filter($ids, function ($id): bool {
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
        return static::find()->where(['status' => PublicationStatus::PUBLISHED->value]);
    }

    /**
     * Returns status labels.
     * 
     * @return array<string, string>
     */
    public static function getStatusLabels(): array
    {
        return PublicationStatus::labels();
    }

    /**
     * Returns status label for current publication.
     */
    public function getStatusLabel(): string
    {
        return $this->getPublicationStatus()->label();
    }
}

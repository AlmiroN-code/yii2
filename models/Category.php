<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\services\SlugServiceInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Category model for hierarchical categories.
 * Requirements: 1.1, 1.4, 2.1, 2.2, 5.3, 5.4, 8.2
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category|null $parent
 * @property Category[] $children
 * @property Publication[] $publications
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%category}}';
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
            [['name'], 'required'],
            [['name', 'meta_title'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['description', 'meta_description'], 'string'],
            [['parent_id', 'sort_order'], 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Родительская категория',
            'name' => 'Название',
            'slug' => 'URL-адрес',
            'description' => 'Описание',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
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

        // Generate slug from name if empty using SlugService via DI
        if (empty($this->slug)) {
            /** @var SlugServiceInterface $slugService */
            $slugService = Yii::$container->get(SlugServiceInterface::class);
            $this->slug = $slugService->generate(
                $this->name,
                self::tableName(),
                $this->isNewRecord ? null : $this->id
            );
        }

        return true;
    }

    /**
     * Gets the parent category.
     */
    public function getParent(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * Gets child categories.
     */
    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
    }

    /**
     * Gets publications in this category.
     */
    public function getPublications(): ActiveQuery
    {
        return $this->hasMany(Publication::class, ['category_id' => 'id']);
    }
}

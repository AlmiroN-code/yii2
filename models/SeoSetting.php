<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель для хранения SEO настроек (глобальных и для сущностей).
 * Requirements: 8.1, 8.2
 *
 * @property int $id
 * @property string $entity_type
 * @property int|null $entity_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $canonical_url
 * @property string $robots
 * @property string $created_at
 * @property string $updated_at
 */
class SeoSetting extends ActiveRecord
{
    public const TYPE_GLOBAL = 'global';
    public const TYPE_PUBLICATION = 'publication';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_PAGE = 'page';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%seo_setting}}';
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
            [['entity_type'], 'required'],
            [['entity_type'], 'in', 'range' => [self::TYPE_GLOBAL, self::TYPE_PUBLICATION, self::TYPE_CATEGORY, self::TYPE_PAGE]],
            [['entity_id'], 'integer'],
            [['entity_id'], 'required', 'when' => fn($model) => $model->entity_type !== self::TYPE_GLOBAL,
                'message' => 'ID сущности обязателен для не-глобальных настроек'],
            [['meta_title', 'og_title'], 'string', 'max' => 255],
            [['meta_keywords', 'og_image', 'canonical_url'], 'string', 'max' => 500],
            [['meta_description', 'og_description'], 'string'],
            [['robots'], 'string', 'max' => 50],
            [['robots'], 'default', 'value' => 'index,follow'],
            [['canonical_url'], 'url', 'defaultScheme' => 'https', 'skipOnEmpty' => true],
            [['og_image'], 'url', 'defaultScheme' => 'https', 'skipOnEmpty' => true],
            [['entity_type', 'entity_id'], 'unique', 'targetAttribute' => ['entity_type', 'entity_id'],
                'message' => 'SEO настройки для этой сущности уже существуют'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'entity_type' => 'Тип сущности',
            'entity_id' => 'ID сущности',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'og_title' => 'OG Title',
            'og_description' => 'OG Description',
            'og_image' => 'OG Image',
            'canonical_url' => 'Canonical URL',
            'robots' => 'Robots',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Получает глобальные SEO настройки.
     */
    public static function getGlobal(): ?self
    {
        return static::findOne(['entity_type' => self::TYPE_GLOBAL]);
    }

    /**
     * Получает или создаёт глобальные SEO настройки.
     */
    public static function getOrCreateGlobal(): self
    {
        $setting = static::getGlobal();
        if ($setting === null) {
            $setting = new self([
                'entity_type' => self::TYPE_GLOBAL,
                'entity_id' => null,
            ]);
        }
        return $setting;
    }

    /**
     * Получает SEO настройки для сущности.
     */
    public static function findByEntity(string $type, int $id): ?self
    {
        return static::findOne([
            'entity_type' => $type,
            'entity_id' => $id,
        ]);
    }

    /**
     * Получает или создаёт SEO настройки для сущности.
     */
    public static function getOrCreateForEntity(string $type, int $id): self
    {
        $setting = static::findByEntity($type, $id);
        if ($setting === null) {
            $setting = new self([
                'entity_type' => $type,
                'entity_id' => $id,
            ]);
        }
        return $setting;
    }

    /**
     * Возвращает список типов сущностей.
     * 
     * @return array<string, string>
     */
    public static function getEntityTypes(): array
    {
        return [
            self::TYPE_GLOBAL => 'Глобальные',
            self::TYPE_PUBLICATION => 'Публикация',
            self::TYPE_CATEGORY => 'Категория',
            self::TYPE_PAGE => 'Страница',
        ];
    }

    /**
     * Возвращает метку типа сущности.
     */
    public function getEntityTypeLabel(): string
    {
        return self::getEntityTypes()[$this->entity_type] ?? $this->entity_type;
    }
}

<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\services\SlugServiceInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель статической страницы.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property bool $is_active
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class Page extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%page}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'content'], 'required'],
            [['title', 'slug', 'meta_title'], 'string', 'max' => 255],
            [['content', 'meta_description'], 'string'],
            [['slug'], 'unique'],
            [['slug'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'Slug может содержать только латинские буквы, цифры и дефис'],
            [['is_active'], 'boolean'],
            [['sort_order'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'slug' => 'URL (slug)',
            'content' => 'Содержимое',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'is_active' => 'Активна',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (empty($this->slug)) {
            /** @var SlugServiceInterface $slugService */
            $slugService = Yii::$container->get(SlugServiceInterface::class);
            $this->slug = $slugService->generate(
                $this->title,
                self::tableName(),
                $this->isNewRecord ? null : $this->id
            );
        }

        return true;
    }

    /**
     * Находит активную страницу по slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug, 'is_active' => true]);
    }

    /**
     * Возвращает все активные страницы.
     */
    public static function findAllActive(): array
    {
        return static::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'title' => SORT_ASC])
            ->all();
    }
}

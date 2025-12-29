<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\services\SlugServiceInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Tag model for publication tagging.
 * Requirements: 1.1, 1.4, 3.1, 5.3, 5.4
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

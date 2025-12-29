<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * PublicationTag junction model for many-to-many relationship.
 * Requirements: 3.2
 *
 * @property int $publication_id
 * @property int $tag_id
 *
 * @property Publication $publication
 * @property Tag $tag
 */
class PublicationTag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%publication_tag}}';
    }

    /**
     * {@inheritdoc}
     * Composite primary key.
     */
    public static function primaryKey(): array
    {
        return ['publication_id', 'tag_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['publication_id', 'tag_id'], 'required'],
            [['publication_id', 'tag_id'], 'integer'],
            [['publication_id'], 'exist', 'skipOnError' => true, 'targetClass' => Publication::class, 'targetAttribute' => ['publication_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
            [['publication_id', 'tag_id'], 'unique', 'targetAttribute' => ['publication_id', 'tag_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'publication_id' => 'Публикация',
            'tag_id' => 'Тег',
        ];
    }

    /**
     * Gets the publication.
     */
    public function getPublication(): ActiveQuery
    {
        return $this->hasOne(Publication::class, ['id' => 'publication_id']);
    }

    /**
     * Gets the tag.
     */
    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }
}

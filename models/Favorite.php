<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * Favorite model.
 * Requirements: 3.1, 3.2
 *
 * @property int $id
 * @property int $user_id
 * @property int $publication_id
 * @property string $created_at
 *
 * @property User $user
 * @property Publication $publication
 */
class Favorite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%favorite}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'publication_id'], 'required'],
            [['user_id', 'publication_id'], 'integer'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['publication_id'], 'exist', 'targetClass' => Publication::class, 'targetAttribute' => 'id'],
            [['user_id', 'publication_id'], 'unique', 'targetAttribute' => ['user_id', 'publication_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'publication_id' => 'Публикация',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets user.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets publication.
     */
    public function getPublication(): ActiveQuery
    {
        return $this->hasOne(Publication::class, ['id' => 'publication_id']);
    }

    /**
     * Toggles favorite status.
     * 
     * @return bool New favorite status (true = added, false = removed)
     */
    public static function toggle(int $userId, int $publicationId): bool
    {
        $favorite = static::findOne([
            'user_id' => $userId,
            'publication_id' => $publicationId,
        ]);

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        $favorite = new static();
        $favorite->user_id = $userId;
        $favorite->publication_id = $publicationId;
        $favorite->save();
        
        return true;
    }

    /**
     * Checks if publication is in favorites.
     */
    public static function isFavorite(int $userId, int $publicationId): bool
    {
        return static::find()
            ->where(['user_id' => $userId, 'publication_id' => $publicationId])
            ->exists();
    }

    /**
     * Gets favorites count for publication.
     */
    public static function getCount(int $publicationId): int
    {
        return (int)static::find()
            ->where(['publication_id' => $publicationId])
            ->count();
    }
}

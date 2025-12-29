<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * UserProfile model.
 * Requirements: 2.1, 2.2, 2.3
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $display_name
 * @property string|null $avatar
 * @property string|null $bio
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserProfile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_profile}}';
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
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['display_name'], 'string', 'max' => 100],
            [['avatar'], 'string', 'max' => 255],
            [['bio'], 'string', 'max' => 1000],
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
            'display_name' => 'Отображаемое имя',
            'avatar' => 'Аватар',
            'bio' => 'О себе',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
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
     * Returns avatar URL or default.
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return Yii::getAlias('@web/uploads/avatars/' . $this->avatar);
        }
        return Yii::getAlias('@web/images/default-avatar.svg');
    }
}

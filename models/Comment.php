<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Comment model.
 * Requirements: 5.1, 5.4, 5.5, 5.7
 *
 * @property int $id
 * @property int $publication_id
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property string|null $guest_email
 * @property string $content
 * @property int $rating
 * @property string $status
 * @property string|null $ip_address
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Publication $publication
 * @property User|null $user
 */
class Comment extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SPAM = 'spam';

    // Запрещённые слова для антиспама
    private static $spamWords = [
        'viagra', 'casino', 'porn', 'xxx', 'buy now', 'click here',
        'free money', 'winner', 'congratulations', 'lottery',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%comment}}';
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
            [['publication_id', 'content'], 'required'],
            [['publication_id', 'user_id', 'rating'], 'integer'],
            [['publication_id'], 'exist', 'targetClass' => Publication::class, 'targetAttribute' => 'id'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [['guest_name'], 'string', 'max' => 100],
            [['guest_email'], 'string', 'max' => 255],
            [['guest_email'], 'email'],
            [['content'], 'string', 'min' => 3, 'max' => 5000],
            [['rating'], 'in', 'range' => [1, 2, 3, 4, 5]],
            [['rating'], 'default', 'value' => 5],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_SPAM]],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'publication_id' => 'Публикация',
            'user_id' => 'Пользователь',
            'guest_name' => 'Имя',
            'guest_email' => 'Email',
            'content' => 'Комментарий',
            'rating' => 'Оценка',
            'status' => 'Статус',
            'ip_address' => 'IP адрес',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Gets publication.
     */
    public function getPublication(): ActiveQuery
    {
        return $this->hasOne(Publication::class, ['id' => 'publication_id']);
    }

    /**
     * Gets user.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Returns author name.
     */
    public function getAuthorName(): string
    {
        if ($this->user) {
            return $this->user->getDisplayName();
        }
        return $this->guest_name ?? 'Гость';
    }

    /**
     * Checks if content contains spam words.
     */
    public function isSpam(): bool
    {
        $content = mb_strtolower($this->content);
        
        foreach (self::$spamWords as $word) {
            if (mb_strpos($content, $word) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Scope for approved comments.
     */
    public static function findApproved(): ActiveQuery
    {
        return static::find()->where(['status' => self::STATUS_APPROVED]);
    }

    /**
     * Returns status labels.
     */
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'На модерации',
            self::STATUS_APPROVED => 'Одобрен',
            self::STATUS_REJECTED => 'Отклонён',
            self::STATUS_SPAM => 'Спам',
        ];
    }

    /**
     * Returns status label.
     */
    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Gets average rating for publication.
     */
    public static function getAverageRating(int $publicationId): ?float
    {
        $avg = static::find()
            ->where(['publication_id' => $publicationId, 'status' => self::STATUS_APPROVED])
            ->average('rating');
        
        return $avg ? round((float)$avg, 1) : null;
    }
}

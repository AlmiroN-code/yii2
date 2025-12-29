<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User model для системы авторизации.
 * Requirements: 1.1-1.9, 1.6, 1.7
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $access_token
 * @property int $status
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 *
 * @property UserProfile $profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;

    const ROLE_USER = 'user';
    const ROLE_AUTHOR = 'author';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
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
            [['username', 'email'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 50],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 
                'message' => 'Имя пользователя может содержать только латинские буквы, цифры, дефис и подчёркивание'],
            [['username'], 'unique', 'message' => 'Это имя пользователя уже занято'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique', 'message' => 'Этот email уже зарегистрирован'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_BANNED]],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['role'], 'string', 'max' => 20],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_AUTHOR, self::ROLE_ADMIN]],
            [['role'], 'default', 'value' => self::ROLE_USER],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password_hash' => 'Пароль',
            'auth_key' => 'Ключ авторизации',
            'access_token' => 'Токен доступа',
            'status' => 'Статус',
            'role' => 'Роль',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username.
     */
    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email.
     */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username or email.
     */
    public static function findByUsernameOrEmail(string $identity): ?self
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['username' => $identity], ['email' => $identity]])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password.
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Sets password hash from plain password.
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates auth key.
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString(32);
    }

    /**
     * Generates access token.
     */
    public function generateAccessToken(): void
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Gets user profile.
     */
    public function getProfile(): ActiveQuery
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * Returns display name (from profile or username).
     */
    public function getDisplayName(): string
    {
        if ($this->profile && $this->profile->display_name) {
            return $this->profile->display_name;
        }
        return $this->username;
    }

    /**
     * Returns status labels.
     */
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_INACTIVE => 'Неактивен',
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_BANNED => 'Заблокирован',
        ];
    }

    /**
     * Returns status label for current user.
     */
    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->status] ?? 'Неизвестно';
    }

    /**
     * Returns role labels.
     */
    public static function getRoleLabels(): array
    {
        return [
            self::ROLE_USER => 'Пользователь',
            self::ROLE_AUTHOR => 'Автор',
            self::ROLE_ADMIN => 'Администратор',
        ];
    }

    /**
     * Returns role label for current user.
     */
    public function getRoleLabel(): string
    {
        return self::getRoleLabels()[$this->role] ?? 'Неизвестно';
    }

    /**
     * Checks if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Checks if user is author.
     */
    public function isAuthor(): bool
    {
        return $this->role === self::ROLE_AUTHOR;
    }

    /**
     * Checks if user can create publications.
     */
    public function canCreatePublication(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_AUTHOR;
    }

    /**
     * Checks if user can edit a specific publication.
     */
    public function canEditPublication(\app\models\Publication $publication): bool
    {
        if ($this->role === self::ROLE_ADMIN) {
            return true;
        }
        return $this->role === self::ROLE_AUTHOR && $publication->author_id === $this->id;
    }
}

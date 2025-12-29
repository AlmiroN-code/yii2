<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use app\enums\UserRole;
use app\enums\UserStatus;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User model для системы авторизации.
 * Requirements: 1.1-1.9, 1.4, 4.2, 5.1, 5.3, 5.4
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
            [['status'], 'in', 'range' => array_column(UserStatus::cases(), 'value')],
            [['status'], 'default', 'value' => UserStatus::ACTIVE->value],
            [['role'], 'string', 'max' => 20],
            [['role'], 'in', 'range' => array_column(UserRole::cases(), 'value')],
            [['role'], 'default', 'value' => UserRole::USER->value],
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
     * Gets the user status as enum.
     */
    public function getUserStatus(): UserStatus
    {
        return UserStatus::tryFrom($this->status) ?? UserStatus::INACTIVE;
    }

    /**
     * Sets the user status from enum.
     */
    public function setUserStatus(UserStatus $status): void
    {
        $this->status = $status->value;
    }

    /**
     * Gets the user role as enum.
     */
    public function getUserRole(): UserRole
    {
        return UserRole::tryFrom($this->role) ?? UserRole::USER;
    }

    /**
     * Sets the user role from enum.
     */
    public function setUserRole(UserRole $role): void
    {
        $this->role = $role->value;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?self
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::ACTIVE->value]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        return static::findOne(['access_token' => $token, 'status' => UserStatus::ACTIVE->value]);
    }

    /**
     * Finds user by username.
     */
    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username, 'status' => UserStatus::ACTIVE->value]);
    }

    /**
     * Finds user by email.
     */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email, 'status' => UserStatus::ACTIVE->value]);
    }

    /**
     * Finds user by username or email.
     */
    public static function findByUsernameOrEmail(string $identity): ?self
    {
        return static::find()
            ->where(['status' => UserStatus::ACTIVE->value])
            ->andWhere(['or', ['username' => $identity], ['email' => $identity]])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
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
        return $this->profile?->display_name ?? $this->username;
    }

    /**
     * Returns status labels.
     * 
     * @return array<int, string>
     */
    public static function getStatusLabels(): array
    {
        return UserStatus::labels();
    }

    /**
     * Returns status label for current user.
     */
    public function getStatusLabel(): string
    {
        return $this->getUserStatus()->label();
    }

    /**
     * Returns role labels.
     * 
     * @return array<string, string>
     */
    public static function getRoleLabels(): array
    {
        return UserRole::labels();
    }

    /**
     * Returns role label for current user.
     */
    public function getRoleLabel(): string
    {
        return $this->getUserRole()->label();
    }

    /**
     * Checks if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->getUserRole() === UserRole::ADMIN;
    }

    /**
     * Checks if user is author.
     */
    public function isAuthor(): bool
    {
        return $this->getUserRole() === UserRole::AUTHOR;
    }

    /**
     * Checks if user can create publications.
     */
    public function canCreatePublication(): bool
    {
        return $this->getUserRole()->canCreatePublication();
    }

    /**
     * Checks if user can edit a specific publication.
     */
    public function canEditPublication(Publication $publication): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->isAuthor() && $publication->author_id === $this->id;
    }
}

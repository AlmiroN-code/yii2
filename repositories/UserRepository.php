<?php

declare(strict_types=1);

namespace app\repositories;

use app\enums\UserStatus;
use app\models\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Реализация репозитория пользователей.
 * Requirements: 2.2, 2.5
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return User::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @return User[]
     */
    public function findAll(): array
    {
        return User::find()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function save(ActiveRecord $model): bool
    {
        return $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ActiveRecord $model): bool
    {
        return $model->delete() !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername(string $username): ?User
    {
        return User::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return User::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsernameOrEmail(string $identity): ?User
    {
        return User::find()
            ->where(['or', ['username' => $identity], ['email' => $identity]])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function findActive(): ActiveQuery
    {
        return User::find()->where(['status' => UserStatus::ACTIVE->value]);
    }
}

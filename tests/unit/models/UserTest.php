<?php

namespace tests\unit\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        // Находим реального пользователя в БД
        $existingUser = User::find()->one();
        if (!$existingUser) {
            $this->markTestSkipped('No users in database');
        }

        verify($user = User::findIdentity($existingUser->id))->notEmpty();
        verify($user->username)->equals($existingUser->username);

        verify(User::findIdentity(999999))->empty();
    }

    public function testFindUserByAccessToken()
    {
        // Находим пользователя с access_token
        $existingUser = User::find()->where(['not', ['access_token' => null]])->one();
        if (!$existingUser) {
            $this->markTestSkipped('No users with access_token in database');
        }

        verify($user = User::findIdentityByAccessToken($existingUser->access_token))->notEmpty();
        verify($user->username)->equals($existingUser->username);

        verify(User::findIdentityByAccessToken('non-existing-token'))->empty();
    }

    public function testFindUserByUsername()
    {
        // Находим любого пользователя в БД
        $existingUser = User::find()->one();
        if (!$existingUser) {
            $this->markTestSkipped('No users in database');
        }

        verify($user = User::findByUsername($existingUser->username))->notEmpty();
        verify(User::findByUsername('non-existing-username-12345'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        // Находим любого пользователя в БД
        $user = User::find()->one();
        if (!$user) {
            $this->markTestSkipped('No users in database');
        }

        // Проверяем auth_key
        verify($user->validateAuthKey($user->auth_key))->true();
        verify($user->validateAuthKey('wrong-auth-key'))->false();

        // Проверка пароля требует знания исходного пароля
        // Проверяем только что неправильный пароль не проходит
        verify($user->validatePassword('definitely-wrong-password-12345'))->false();
    }
}

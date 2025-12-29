<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm - форма регистрации пользователя.
 * Requirements: 1.5, 1.6, 1.7
 */
class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'email', 'password', 'password_confirm'], 'required'],
            
            // Username
            [['username'], 'string', 'min' => 3, 'max' => 50],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/',
                'message' => 'Имя пользователя может содержать только латинские буквы, цифры, дефис и подчёркивание'],
            [['username'], 'unique', 'targetClass' => User::class,
                'message' => 'Это имя пользователя уже занято'],
            
            // Email
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique', 'targetClass' => User::class,
                'message' => 'Этот email уже зарегистрирован'],
            
            // Password
            [['password'], 'string', 'min' => 6, 'max' => 72],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password',
                'message' => 'Пароли не совпадают'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
            'password_confirm' => 'Подтверждение пароля',
        ];
    }

    /**
     * Registers a new user.
     * 
     * @return User|null the saved user model or null if registration failed
     */
    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if ($user->save()) {
            // Создаём пустой профиль
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->save(false);
            
            return $user;
        }

        return null;
    }
}

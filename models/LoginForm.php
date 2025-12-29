<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm - форма входа в систему.
 * Requirements: 1.1-1.4
 *
 * @property-read User|null $user
 */
class LoginForm extends Model
{
    public $identity;  // username или email
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['identity', 'password'], 'required'],
            [['rememberMe'], 'boolean'],
            [['password'], 'validatePassword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'identity' => 'Имя пользователя или Email',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    /**
     * Validates the password.
     */
    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // Единое сообщение об ошибке для безопасности
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверное имя пользователя или пароль.');
            }
        }
    }

    /**
     * Logs in a user.
     * 
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            // 30 дней если "запомнить меня"
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
            return Yii::$app->user->login($this->getUser(), $duration);
        }
        return false;
    }

    /**
     * Finds user by identity (username or email).
     * 
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsernameOrEmail($this->identity);
        }

        return $this->_user;
    }
}

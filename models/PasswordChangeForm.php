<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * PasswordChangeForm - форма смены пароля.
 * Requirements: 2.5, 2.6
 */
class PasswordChangeForm extends Model
{
    public $current_password;
    public $new_password;
    public $confirm_password;

    private $_user;

    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['current_password', 'new_password', 'confirm_password'], 'required'],
            [['current_password'], 'validateCurrentPassword'],
            [['new_password'], 'string', 'min' => 6, 'max' => 72],
            [['confirm_password'], 'compare', 'compareAttribute' => 'new_password',
                'message' => 'Пароли не совпадают'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'current_password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'confirm_password' => 'Подтверждение пароля',
        ];
    }

    /**
     * Validates current password.
     */
    public function validateCurrentPassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->current_password)) {
                $this->addError($attribute, 'Неверный текущий пароль.');
            }
        }
    }

    /**
     * Changes password.
     */
    public function changePassword(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_user->setPassword($this->new_password);
        return $this->_user->save(false);
    }
}

<?php

declare(strict_types=1);

namespace app\services;

use app\enums\UserRole;
use app\enums\UserStatus;
use app\models\User;
use app\models\UserProfile;
use app\repositories\UserRepositoryInterface;
use Yii;

/**
 * Сервис для работы с пользователями.
 * Requirements: 1.3, 3.1, 3.2, 3.4
 */
class UserService implements UserServiceInterface
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function register(array $data): ?User
    {
        $user = new User();
        $user->username = $data['username'] ?? '';
        $user->email = $data['email'] ?? '';
        
        if (!empty($data['password'])) {
            $user->setPassword($data['password']);
        }
        
        $user->generateAuthKey();
        $user->status = 1; // ACTIVE
        $user->role = 'user';
        
        if (!$this->userRepository->save($user)) {
            Yii::error('Failed to register user: ' . json_encode($user->errors), __METHOD__);
            return null;
        }
        
        // Создаём профиль пользователя
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->display_name = $data['display_name'] ?? $user->username;
        
        if (!$profile->save()) {
            Yii::error('Failed to create user profile: ' . json_encode($profile->errors), __METHOD__);
        }
        
        return $user;
    }


    /**
     * {@inheritdoc}
     */
    public function updateProfile(User $user, array $data): bool
    {
        // Обновляем данные пользователя
        if (isset($data['username'])) {
            $user->username = $data['username'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        
        if (!$this->userRepository->save($user)) {
            Yii::error('Failed to update user: ' . json_encode($user->errors), __METHOD__);
            return false;
        }
        
        // Обновляем профиль если есть данные
        $profile = $user->profile;
        if ($profile === null) {
            $profile = new UserProfile();
            $profile->user_id = $user->id;
        }
        
        $profileAttributes = ['display_name', 'bio', 'avatar', 'website', 'location'];
        foreach ($profileAttributes as $attr) {
            if (isset($data[$attr])) {
                $profile->$attr = $data[$attr];
            }
        }
        
        if (!$profile->save()) {
            Yii::error('Failed to update user profile: ' . json_encode($profile->errors), __METHOD__);
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        $user->setPassword($newPassword);
        $user->generateAuthKey();
        
        if ($this->userRepository->save($user)) {
            return true;
        }
        
        Yii::error('Failed to change password for user ID: ' . $user->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function changeRole(User $user, string $role): bool
    {
        $user->role = $role;
        
        if ($this->userRepository->save($user)) {
            return true;
        }
        
        Yii::error('Failed to change role for user ID: ' . $user->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ban(User $user): bool
    {
        $user->status = 0; // BANNED
        
        if ($this->userRepository->save($user)) {
            return true;
        }
        
        Yii::error('Failed to ban user ID: ' . $user->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function activate(User $user): bool
    {
        $user->status = 1; // ACTIVE
        
        if ($this->userRepository->save($user)) {
            return true;
        }
        
        Yii::error('Failed to activate user ID: ' . $user->id, __METHOD__);
        return false;
    }
}

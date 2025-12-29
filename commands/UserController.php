<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;
use app\models\UserProfile;

/**
 * User management commands.
 */
class UserController extends Controller
{
    /**
     * Resets user password.
     * Usage: php yii user/reset-password admin newpassword
     */
    public function actionResetPassword(string $username, string $password): int
    {
        $user = User::find()->where(['username' => $username])->one();
        
        if (!$user) {
            $this->stderr("User '$username' not found.\n");
            return ExitCode::DATAERR;
        }

        $user->setPassword($password);
        if ($user->save(false)) {
            $this->stdout("Password for '$username' has been reset.\n");
            return ExitCode::OK;
        }

        $this->stderr("Failed to reset password.\n");
        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * Creates a new user.
     * Usage: php yii user/create username email password
     */
    public function actionCreate(string $username, string $email, string $password): int
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if ($user->save()) {
            // Create profile
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->save(false);
            
            $this->stdout("User '$username' created successfully.\n");
            return ExitCode::OK;
        }

        foreach ($user->errors as $attr => $errors) {
            $this->stderr("$attr: " . implode(', ', $errors) . "\n");
        }
        return ExitCode::DATAERR;
    }
}

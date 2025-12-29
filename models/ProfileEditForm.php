<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * ProfileEditForm - форма редактирования профиля.
 * Requirements: 2.2, 2.3
 */
class ProfileEditForm extends Model
{
    public $display_name;
    public $bio;
    
    /** @var UploadedFile */
    public $avatarFile;

    private $_user;
    private $_profile;

    public function __construct(User $user, $config = [])
    {
        $this->_user = $user;
        $this->_profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        
        $this->display_name = $this->_profile->display_name;
        $this->bio = $this->_profile->bio;
        
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['display_name'], 'string', 'max' => 100],
            [['bio'], 'string', 'max' => 1000],
            [['avatarFile'], 'file', 
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'maxSize' => 2 * 1024 * 1024, // 2MB
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'display_name' => 'Отображаемое имя',
            'bio' => 'О себе',
            'avatarFile' => 'Аватар',
        ];
    }

    /**
     * Saves profile changes.
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_profile->display_name = $this->display_name;
        $this->_profile->bio = $this->bio;

        // Handle avatar upload with ImageOptimizer
        if ($this->avatarFile) {
            $path = Yii::getAlias('@webroot/uploads/avatars/');
            
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            // Delete old avatar and thumbnails
            if ($this->_profile->avatar) {
                $optimizer = new \app\services\ImageOptimizer();
                $optimizer->delete($path . $this->_profile->avatar);
            }

            $filename = $this->_user->id . '_' . time() . '.' . $this->avatarFile->extension;
            $tempPath = $path . $filename;
            
            if ($this->avatarFile->saveAs($tempPath)) {
                // Optimize and convert to WebP
                $optimizer = new \app\services\ImageOptimizer();
                $optimizedPath = $optimizer->optimize($tempPath, ['maxWidth' => 400]);
                $optimizer->createThumbnails($optimizedPath);
                
                $this->_profile->avatar = basename($optimizedPath);
            }
        }

        return $this->_profile->save();
    }

    public function getProfile(): UserProfile
    {
        return $this->_profile;
    }
}

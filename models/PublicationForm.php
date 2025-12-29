<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\services\ImageOptimizer;

/**
 * Форма создания/редактирования публикации.
 * Requirements: 3.1-3.6
 */
class PublicationForm extends Model
{
    public $title;
    public $slug;
    public $content;
    public $excerpt;
    public $category_id;
    public $tagIds = [];
    public $featured_image;
    public $status;
    
    /** @var UploadedFile */
    public $imageFile;
    
    /** @var Publication|null */
    private $_publication;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'content'], 'required'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['content', 'excerpt'], 'string'],
            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id'],
            [['status'], 'in', 'range' => [Publication::STATUS_DRAFT, Publication::STATUS_PUBLISHED]],
            [['status'], 'default', 'value' => Publication::STATUS_DRAFT],
            [['tagIds'], 'safe'],
            [['imageFile'], 'file', 'extensions' => 'jpg, jpeg, png, gif, webp', 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Заголовок',
            'slug' => 'URL-адрес',
            'content' => 'Содержимое',
            'excerpt' => 'Краткое описание',
            'category_id' => 'Категория',
            'tagIds' => 'Теги',
            'featured_image' => 'Обложка',
            'imageFile' => 'Обложка',
            'status' => 'Статус',
        ];
    }

    /**
     * Загружает данные из существующей публикации.
     */
    public function loadFromPublication(Publication $publication): void
    {
        $this->_publication = $publication;
        $this->title = $publication->title;
        $this->slug = $publication->slug;
        $this->content = $publication->content;
        $this->excerpt = $publication->excerpt;
        $this->category_id = $publication->category_id;
        $this->tagIds = $publication->getTagIds();
        $this->featured_image = $publication->featured_image;
        $this->status = $publication->status;
    }

    /**
     * Сохраняет публикацию.
     */
    public function save(int $authorId): ?Publication
    {
        if (!$this->validate()) {
            return null;
        }

        $publication = $this->_publication ?? new Publication();
        
        $publication->title = $this->title;
        $publication->slug = $this->slug ?: null;
        $publication->content = $this->content;
        $publication->excerpt = $this->excerpt;
        $publication->category_id = $this->category_id ?: null;
        $publication->tagIds = $this->tagIds;
        $publication->status = $this->status;
        
        // Устанавливаем автора только для новых публикаций
        if ($publication->isNewRecord) {
            $publication->author_id = $authorId;
        }

        // Обработка изображения
        if ($this->imageFile) {
            $imagePath = $this->uploadImage();
            if ($imagePath) {
                // Удаляем старое изображение
                if ($publication->featured_image) {
                    $this->deleteOldImage($publication->featured_image);
                }
                $publication->featured_image = $imagePath;
            }
        }

        if ($publication->save()) {
            return $publication;
        }

        return null;
    }

    /**
     * Загружает и оптимизирует изображение.
     */
    protected function uploadImage(): ?string
    {
        if (!$this->imageFile) {
            return null;
        }

        $uploadDir = Yii::getAlias('@webroot/uploads/publications');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = Yii::$app->security->generateRandomString(16) . '.' . $this->imageFile->extension;
        $filepath = $uploadDir . '/' . $filename;

        if ($this->imageFile->saveAs($filepath)) {
            // Оптимизация через ImageOptimizer
            try {
                $optimizer = new ImageOptimizer();
                $optimizer->optimize($filepath);
            } catch (\Exception $e) {
                Yii::warning('Image optimization failed: ' . $e->getMessage());
            }
            
            return '/uploads/publications/' . $filename;
        }

        return null;
    }

    /**
     * Удаляет старое изображение.
     */
    protected function deleteOldImage(string $imagePath): void
    {
        $fullPath = Yii::getAlias('@webroot') . $imagePath;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Возвращает публикацию.
     */
    public function getPublication(): ?Publication
    {
        return $this->_publication;
    }
}

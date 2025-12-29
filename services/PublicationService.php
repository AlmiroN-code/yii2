<?php

declare(strict_types=1);

namespace app\services;

use app\enums\PublicationStatus;
use app\models\Publication;
use app\repositories\PublicationRepositoryInterface;
use Yii;

/**
 * Сервис для работы с публикациями.
 * Requirements: 1.2, 3.1, 3.2, 3.3, 3.4
 */
class PublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly SlugServiceInterface $slugService,
        private readonly PublicationRepositoryInterface $publicationRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data, int $authorId): ?Publication
    {
        $publication = new Publication();
        $publication->author_id = $authorId;
        
        $this->populatePublication($publication, $data);
        
        // Генерируем slug если не задан
        if (empty($publication->slug) && !empty($publication->title)) {
            $publication->slug = $this->slugService->generate(
                $publication->title,
                Publication::tableName()
            );
        }
        
        // Устанавливаем статус по умолчанию через enum
        if (empty($publication->status)) {
            $publication->setPublicationStatus(PublicationStatus::DRAFT);
        }
        
        if ($this->publicationRepository->save($publication)) {
            return $publication;
        }
        
        Yii::error('Failed to create publication: ' . json_encode($publication->errors), __METHOD__);
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function update(Publication $publication, array $data): bool
    {
        $this->populatePublication($publication, $data);
        
        // Обновляем slug если изменился title и slug пустой
        if (empty($publication->slug) && !empty($publication->title)) {
            $publication->slug = $this->slugService->generate(
                $publication->title,
                Publication::tableName(),
                $publication->id
            );
        }
        
        if ($this->publicationRepository->save($publication)) {
            return true;
        }
        
        Yii::error('Failed to update publication: ' . json_encode($publication->errors), __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Publication $publication): bool
    {
        if ($this->publicationRepository->delete($publication)) {
            return true;
        }
        
        Yii::error('Failed to delete publication ID: ' . $publication->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(Publication $publication): bool
    {
        $publication->setPublicationStatus(PublicationStatus::PUBLISHED);
        
        if (empty($publication->published_at)) {
            $publication->published_at = date('Y-m-d H:i:s');
        }
        
        if ($this->publicationRepository->save($publication)) {
            return true;
        }
        
        Yii::error('Failed to publish publication ID: ' . $publication->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function archive(Publication $publication): bool
    {
        $publication->setPublicationStatus(PublicationStatus::ARCHIVED);
        
        if ($this->publicationRepository->save($publication)) {
            return true;
        }
        
        Yii::error('Failed to archive publication ID: ' . $publication->id, __METHOD__);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementViews(Publication $publication): void
    {
        $publication->updateCounters(['views' => 1]);
    }

    /**
     * Заполняет публикацию данными из массива.
     */
    private function populatePublication(Publication $publication, array $data): void
    {
        $allowedAttributes = [
            'title',
            'slug',
            'excerpt',
            'content',
            'featured_image',
            'status',
            'category_id',
            'meta_title',
            'meta_description',
            'tagIds',
        ];
        
        foreach ($allowedAttributes as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $publication->$attribute = $data[$attribute];
            }
        }
    }
}

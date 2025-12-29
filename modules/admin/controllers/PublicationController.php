<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\enums\PublicationStatus;
use app\models\Category;
use app\models\Publication;
use app\models\Tag;
use app\repositories\PublicationRepositoryInterface;
use app\services\PublicationServiceInterface;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * PublicationController implements CRUD actions for Publication model in admin panel.
 * Requirements: 2.1, 3.3, 4.1
 */
class PublicationController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly PublicationServiceInterface $publicationService,
        private readonly PublicationRepositoryInterface $publicationRepository,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect(['/admin/auth/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'publish' => ['POST'],
                    'archive' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Publication models.
     * Requirements: 2.1, 4.1
     */
    public function actionIndex(): string
    {
        $status = Yii::$app->request->get('status');
        
        $query = Publication::find()->with(['category', 'tags', 'author']);
        
        // Фильтрация по статусу с использованием enum
        if ($status !== null) {
            $statusEnum = PublicationStatus::tryFrom($status);
            if ($statusEnum !== null) {
                $query->andWhere(['status' => $statusEnum->value]);
            }
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'statuses' => PublicationStatus::labels(),
            'currentStatus' => $status,
        ]);
    }

    /**
     * Creates a new Publication model.
     * Requirements: 3.3
     */
    public function actionCreate()
    {
        $model = new Publication();

        if ($model->load(Yii::$app->request->post())) {
            $model->tagIds = Yii::$app->request->post('Publication')['tagIds'] ?? [];
            
            // Handle image upload
            $this->handleImageUpload($model);
            
            // Устанавливаем автора (админ)
            $model->author_id = Yii::$app->user->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Публикация успешно создана.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => $this->getCategoryList(),
            'tags' => Tag::find()->orderBy('name')->all(),
            'statuses' => PublicationStatus::labels(),
        ]);
    }

    /**
     * Updates an existing Publication model.
     * Requirements: 3.3, 4.1
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $model->tagIds = $model->getTagIds();

        if ($model->load(Yii::$app->request->post())) {
            $model->tagIds = Yii::$app->request->post('Publication')['tagIds'] ?? [];
            
            // Handle image upload
            $this->handleImageUpload($model);
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Публикация успешно обновлена.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => $this->getCategoryList(),
            'tags' => Tag::find()->orderBy('name')->all(),
            'statuses' => PublicationStatus::labels(),
        ]);
    }

    /**
     * Publishes a publication.
     * Requirements: 3.3, 4.1
     */
    public function actionPublish(int $id)
    {
        $model = $this->findModel($id);
        
        if ($this->publicationService->publish($model)) {
            Yii::$app->session->setFlash('success', 'Публикация опубликована.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось опубликовать публикацию.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Archives a publication.
     * Requirements: 3.3, 4.1
     */
    public function actionArchive(int $id)
    {
        $model = $this->findModel($id);
        
        if ($this->publicationService->archive($model)) {
            Yii::$app->session->setFlash('success', 'Публикация архивирована.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось архивировать публикацию.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Publication model.
     * Requirements: 3.3
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        
        if ($this->publicationService->delete($model)) {
            // Delete featured image if exists
            if ($model->featured_image) {
                $imagePath = Yii::getAlias('@webroot') . $model->featured_image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            Yii::$app->session->setFlash('success', 'Публикация успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить публикацию.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Publication model based on its primary key value.
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Publication
    {
        $model = $this->publicationRepository->findById($id);
        
        if ($model === null) {
            throw new NotFoundHttpException('Публикация не найдена.');
        }

        return $model;
    }

    /**
     * Returns category list for dropdown.
     * @return array<int, string>
     */
    protected function getCategoryList(): array
    {
        return Category::find()
            ->select(['name', 'id'])
            ->orderBy('name')
            ->indexBy('id')
            ->column();
    }

    /**
     * Handles image upload for publication.
     */
    protected function handleImageUpload(Publication $model): void
    {
        $uploadedFile = UploadedFile::getInstance($model, 'featured_image');
        
        if ($uploadedFile) {
            $uploadDir = Yii::getAlias('@webroot/uploads/publications');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '.' . $uploadedFile->extension;
            $filePath = $uploadDir . '/' . $fileName;
            
            if ($uploadedFile->saveAs($filePath)) {
                // Delete old image if exists
                if ($model->getOldAttribute('featured_image')) {
                    $oldPath = Yii::getAlias('@webroot') . $model->getOldAttribute('featured_image');
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $model->featured_image = '/uploads/publications/' . $fileName;
            }
        } elseif (!$model->isNewRecord) {
            // Keep old image if no new upload
            $model->featured_image = $model->getOldAttribute('featured_image');
        }
    }
}

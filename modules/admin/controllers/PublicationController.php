<?php

namespace app\modules\admin\controllers;

use app\models\Category;
use app\models\Publication;
use app\models\Tag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * PublicationController implements CRUD actions for Publication model.
 * Requirements: 1.1, 1.2, 1.3, 1.4, 5.3
 */
class PublicationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
                ],
            ],
        ];
    }

    /**
     * Lists all Publication models.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Publication::find()->with(['category', 'tags']),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Publication model.
     */
    public function actionCreate()
    {
        $model = new Publication();

        if ($model->load(Yii::$app->request->post())) {
            $model->tagIds = Yii::$app->request->post('Publication')['tagIds'] ?? [];
            
            // Handle image upload
            $this->handleImageUpload($model);
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Публикация успешно создана.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => $this->getCategoryList(),
            'tags' => Tag::find()->orderBy('name')->all(),
        ]);
    }

    /**
     * Updates an existing Publication model.
     */
    public function actionUpdate($id)
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
        ]);
    }

    /**
     * Deletes an existing Publication model.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete featured image if exists
        if ($model->featured_image) {
            $imagePath = Yii::getAlias('@webroot') . $model->featured_image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Публикация успешно удалена.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Publication model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Publication::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Публикация не найдена.');
    }

    /**
     * Returns category list for dropdown.
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

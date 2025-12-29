<?php

namespace app\controllers;

use Yii;
use app\models\Publication;
use app\models\PublicationForm;
use app\models\Category;
use app\models\Tag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * PublicationController handles frontend publication display and author management.
 * Requirements: 3.1-3.6, 4.1-4.6, 5.1-5.5
 */
class PublicationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete', 'my'],
                'rules' => [
                    [
                        'actions' => ['create', 'my'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->canCreatePublication();
                        },
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('У вас нет прав для создания публикаций.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    /**
     * Displays paginated list of published publications.
     * Requirements: 4.1
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Publication::findPublished()
                ->with(['category', 'tags'])
                ->orderBy(['published_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single publication by slug.
     * Requirements: 4.2
     *
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException if publication not found
     */
    public function actionView(string $slug): string
    {
        $model = Publication::findPublished()
            ->with(['category', 'tags'])
            ->andWhere(['slug' => $slug])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Публикация не найдена.');
        }

        // Increment views counter
        $model->updateCounters(['views' => 1]);

        // Breadcrumbs
        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forPublication($model);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new publication.
     * Requirements: 3.1-3.6
     */
    public function actionCreate()
    {
        $model = new PublicationForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            $publication = $model->save(Yii::$app->user->id);
            if ($publication) {
                Yii::$app->session->setFlash('success', 'Публикация создана.');
                return $this->redirect(['view', 'slug' => $publication->slug]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => Category::find()->orderBy('name')->all(),
            'tags' => Tag::find()->orderBy('name')->all(),
        ]);
    }

    /**
     * Updates an existing publication.
     * Requirements: 4.1-4.6
     */
    public function actionUpdate(int $id)
    {
        $publication = $this->findModel($id);
        
        // Проверка прав
        if (!Yii::$app->user->identity->canEditPublication($publication)) {
            throw new ForbiddenHttpException('Вы можете редактировать только свои публикации.');
        }

        $model = new PublicationForm();
        $model->loadFromPublication($publication);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            $publication = $model->save(Yii::$app->user->id);
            if ($publication) {
                Yii::$app->session->setFlash('success', 'Публикация обновлена.');
                return $this->redirect(['view', 'slug' => $publication->slug]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'publication' => $publication,
            'categories' => Category::find()->orderBy('name')->all(),
            'tags' => Tag::find()->orderBy('name')->all(),
        ]);
    }

    /**
     * Deletes a publication.
     * Requirements: 4.5
     */
    public function actionDelete(int $id)
    {
        $publication = $this->findModel($id);
        
        // Проверка прав
        if (!Yii::$app->user->identity->canEditPublication($publication)) {
            throw new ForbiddenHttpException('Вы можете удалять только свои публикации.');
        }

        $publication->delete();
        Yii::$app->session->setFlash('success', 'Публикация удалена.');
        return $this->redirect(['my']);
    }

    /**
     * Lists author's publications.
     * Requirements: 5.1-5.5
     */
    public function actionMy(): string
    {
        $status = Yii::$app->request->get('status');
        
        $query = Publication::find()
            ->where(['author_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC]);
        
        if ($status && \in_array($status, [Publication::STATUS_DRAFT, Publication::STATUS_PUBLISHED], true)) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('my', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Finds publication by ID.
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Publication
    {
        $model = Publication::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Публикация не найдена.');
        }
        return $model;
    }
}

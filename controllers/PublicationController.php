<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\enums\PublicationStatus;
use app\models\Publication;
use app\models\PublicationForm;
use app\models\Category;
use app\models\Tag;
use app\repositories\PublicationRepositoryInterface;
use app\services\PublicationServiceInterface;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * PublicationController handles frontend publication display and author management.
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
                'only' => ['create', 'update', 'delete', 'my'],
                'rules' => [
                    [
                        'actions' => ['create', 'my'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => fn() => Yii::$app->user->identity->canCreatePublication(),
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
     * Requirements: 2.1, 4.1
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->publicationRepository->findPublished()
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
     * Requirements: 2.1, 4.1, 8.2, 8.7, 8.8
     */
    public function actionView(string $slug): string
    {
        $model = $this->publicationRepository->findPublished()
            ->with(['category', 'tags'])
            ->andWhere(['slug' => $slug])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Публикация не найдена.');
        }

        // Increment views counter via service
        $this->publicationService->incrementViews($model);

        // Breadcrumbs
        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forPublication($model);

        // SEO: Meta tags
        $seo = Yii::$app->seo;
        $seo->title = $model->meta_title ?: $model->title;
        $seo->description = $model->meta_description ?: mb_substr(strip_tags($model->content), 0, 160);
        $seo->canonicalUrl = \yii\helpers\Url::to(['/publication/view', 'slug' => $model->slug], true);
        
        // SEO: Open Graph
        $seo->ogTags = [
            'og:title' => $model->meta_title ?: $model->title,
            'og:description' => $model->meta_description ?: mb_substr(strip_tags($model->content), 0, 160),
            'og:type' => 'article',
            'og:url' => \yii\helpers\Url::to(['/publication/view', 'slug' => $model->slug], true),
            'og:image' => $model->featured_image ? \yii\helpers\Url::to($model->featured_image, true) : null,
        ];

        // SEO: Schema.org Article
        /** @var \app\services\SeoServiceInterface $seoService */
        $seoService = Yii::$container->get(\app\services\SeoServiceInterface::class);
        $seo->schemaOrg = $seoService->getArticleSchema($model);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new publication.
     * Requirements: 3.3
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
     * Requirements: 3.3
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
     * Requirements: 3.3
     */
    public function actionDelete(int $id)
    {
        $publication = $this->findModel($id);
        
        // Проверка прав
        if (!Yii::$app->user->identity->canEditPublication($publication)) {
            throw new ForbiddenHttpException('Вы можете удалять только свои публикации.');
        }

        // Используем сервис для удаления
        $this->publicationService->delete($publication);
        Yii::$app->session->setFlash('success', 'Публикация удалена.');
        return $this->redirect(['my']);
    }

    /**
     * Lists author's publications.
     * Requirements: 2.1, 4.1
     */
    public function actionMy(): string
    {
        $status = Yii::$app->request->get('status');
        
        $query = $this->publicationRepository->findByAuthor(Yii::$app->user->id)
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Фильтрация по статусу с использованием enum
        if ($status !== null) {
            $statusEnum = PublicationStatus::tryFrom($status);
            if ($statusEnum !== null && \in_array($statusEnum, [PublicationStatus::DRAFT, PublicationStatus::PUBLISHED], true)) {
                $query->andWhere(['status' => $statusEnum->value]);
            }
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
     * Finds publication by ID using repository.
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
}

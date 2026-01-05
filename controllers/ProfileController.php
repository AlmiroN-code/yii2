<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\enums\PublicationStatus;
use app\models\User;
use app\models\Favorite;
use app\models\ProfileEditForm;
use app\models\PasswordChangeForm;
use app\repositories\UserRepositoryInterface;
use app\repositories\PublicationRepositoryInterface;
use app\services\UserServiceInterface;
use app\components\Breadcrumbs;

/**
 * ProfileController - контроллер профилей пользователей.
 * Requirements: 2.2, 3.3
 */
class ProfileController extends Controller
{
    private $userService;
    private $userRepository;
    private $publicationRepository;

    public function __construct(
        $id,
        $module,
        UserServiceInterface $userService,
        UserRepositoryInterface $userRepository,
        PublicationRepositoryInterface $publicationRepository,
        array $config = []
    ) {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->publicationRepository = $publicationRepository;
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
                'only' => ['edit', 'password'],
                'rules' => [
                    [
                        'actions' => ['edit', 'password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Displays user profile.
     * Requirements: 2.2
     */
    public function actionView(string $username): string
    {
        $user = $this->findUser($username);
        
        $this->view->params['breadcrumbs'] = Breadcrumbs::forProfile($user);

        return $this->render('view', [
            'user' => $user,
        ]);
    }

    /**
     * Edits user profile.
     * Requirements: 2.2, 3.3
     */
    public function actionEdit(string $username)
    {
        $user = $this->findUser($username);
        $this->checkOwner($user);

        $model = new ProfileEditForm($user);

        if ($model->load(Yii::$app->request->post())) {
            $model->avatarFile = UploadedFile::getInstance($model, 'avatarFile');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Профиль успешно обновлён.');
                return $this->redirect(['view', 'username' => $username]);
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Changes user password.
     * Requirements: 3.3
     */
    public function actionPassword(string $username)
    {
        $user = $this->findUser($username);
        $this->checkOwner($user);

        $model = new PasswordChangeForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
            return $this->redirect(['view', 'username' => $username]);
        }

        return $this->render('password', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Displays user favorites.
     * Requirements: 2.2
     */
    public function actionFavorites(string $username): string
    {
        $user = $this->findUser($username);

        $favorites = Favorite::find()
            ->where(['user_id' => $user->id])
            ->with(['publication', 'publication.category'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $this->view->params['breadcrumbs'] = Breadcrumbs::forProfile($user, 'Избранное');

        return $this->render('favorites', [
            'user' => $user,
            'favorites' => $favorites,
        ]);
    }


    /**
     * Displays user publications.
     * Requirements: 2.2
     */
    public function actionPublications(string $username): string
    {
        $user = $this->findUser($username);

        $query = $this->publicationRepository->findByAuthor($user->id)
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Показываем только опубликованные для других пользователей
        if (Yii::$app->user->isGuest || Yii::$app->user->id !== $user->id) {
            $query->andWhere(['status' => 'published']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->view->params['breadcrumbs'] = Breadcrumbs::forProfile($user, 'Публикации');

        return $this->render('publications', [
            'user' => $user,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds user by username using repository.
     * @throws NotFoundHttpException
     */
    protected function findUser(string $username): User
    {
        $user = $this->userRepository->findByUsername($username);
        
        if ($user === null) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }

        return $user;
    }

    /**
     * Checks if current user is the owner.
     * @throws ForbiddenHttpException
     */
    protected function checkOwner(User $user): void
    {
        if (Yii::$app->user->id !== $user->id) {
            throw new ForbiddenHttpException('У вас нет доступа к этой странице.');
        }
    }
}

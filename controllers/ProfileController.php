<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\models\User;
use app\models\ProfileEditForm;
use app\models\PasswordChangeForm;

/**
 * ProfileController - контроллер профилей пользователей.
 * Requirements: 2.1-2.7
 */
class ProfileController extends Controller
{
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
     * Requirements: 2.1
     */
    public function actionView(string $username): string
    {
        $user = $this->findUser($username);
        
        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forProfile($user);

        return $this->render('view', [
            'user' => $user,
        ]);
    }

    /**
     * Edits user profile.
     * Requirements: 2.2, 2.3, 2.4
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
     * Requirements: 2.5, 2.6
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
     * Requirements: 3.3
     */
    public function actionFavorites(string $username): string
    {
        $user = $this->findUser($username);

        $favorites = \app\models\Favorite::find()
            ->where(['user_id' => $user->id])
            ->with(['publication', 'publication.category'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forProfile($user, 'Избранное');

        return $this->render('favorites', [
            'user' => $user,
            'favorites' => $favorites,
        ]);
    }

    /**
     * Displays user publications.
     */
    public function actionPublications(string $username): string
    {
        $user = $this->findUser($username);

        $query = \app\models\Publication::find()
            ->where(['author_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC]);
        
        // Показываем только опубликованные для других пользователей
        if (Yii::$app->user->isGuest || Yii::$app->user->id !== $user->id) {
            $query->andWhere(['status' => \app\models\Publication::STATUS_PUBLISHED]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->view->params['breadcrumbs'] = \app\components\Breadcrumbs::forProfile($user, 'Публикации');

        return $this->render('publications', [
            'user' => $user,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds user by username.
     * @throws NotFoundHttpException
     */
    protected function findUser(string $username): User
    {
        $user = User::findByUsername($username);
        
        if (!$user) {
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

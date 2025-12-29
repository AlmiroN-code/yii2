<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\enums\UserRole;
use app\enums\UserStatus;
use app\models\User;
use app\repositories\UserRepositoryInterface;
use app\services\UserServiceInterface;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * UserController - управление пользователями в админке.
 * Requirements: 2.2, 3.3, 4.2
 */
class UserController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly UserServiceInterface $userService,
        private readonly UserRepositoryInterface $userRepository,
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
                        'matchCallback' => fn() => Yii::$app->user->identity->isAdmin(),
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('Доступ запрещён. Только для администраторов.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'ban' => ['POST'],
                    'activate' => ['POST'],
                    'change-role' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Список пользователей.
     * Requirements: 2.2, 4.2
     */
    public function actionIndex(): string
    {
        $role = Yii::$app->request->get('role');
        $status = Yii::$app->request->get('status');

        $query = User::find()->orderBy(['created_at' => SORT_DESC]);
        
        // Фильтрация по роли с использованием enum
        if ($role !== null) {
            $roleEnum = UserRole::tryFrom($role);
            if ($roleEnum !== null) {
                $query->andWhere(['role' => $roleEnum->value]);
            }
        }
        
        // Фильтрация по статусу с использованием enum
        if ($status !== null && $status !== '') {
            $statusEnum = UserStatus::tryFrom((int)$status);
            if ($statusEnum !== null) {
                $query->andWhere(['status' => $statusEnum->value]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'roles' => UserRole::labels(),
            'statuses' => UserStatus::labels(),
            'currentRole' => $role,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Редактирование пользователя.
     * Requirements: 3.3, 4.2
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь обновлён.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'roles' => UserRole::labels(),
            'statuses' => UserStatus::labels(),
        ]);
    }

    /**
     * Изменение роли пользователя.
     * Requirements: 3.3, 4.2
     */
    public function actionChangeRole(int $id)
    {
        $model = $this->findModel($id);
        $newRole = Yii::$app->request->post('role');
        
        // Нельзя изменить роль себе
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Нельзя изменить свою роль.');
            return $this->redirect(['index']);
        }
        
        $roleEnum = UserRole::tryFrom($newRole);
        if ($roleEnum === null) {
            Yii::$app->session->setFlash('error', 'Некорректная роль.');
            return $this->redirect(['index']);
        }
        
        if ($this->userService->changeRole($model, $roleEnum)) {
            Yii::$app->session->setFlash('success', 'Роль пользователя изменена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось изменить роль.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Блокировка пользователя.
     * Requirements: 3.3, 4.2
     */
    public function actionBan(int $id)
    {
        $model = $this->findModel($id);
        
        // Нельзя заблокировать себя
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Нельзя заблокировать свой аккаунт.');
            return $this->redirect(['index']);
        }
        
        if ($this->userService->ban($model)) {
            Yii::$app->session->setFlash('success', 'Пользователь заблокирован.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось заблокировать пользователя.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Активация пользователя.
     * Requirements: 3.3, 4.2
     */
    public function actionActivate(int $id)
    {
        $model = $this->findModel($id);
        
        if ($this->userService->activate($model)) {
            Yii::$app->session->setFlash('success', 'Пользователь активирован.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось активировать пользователя.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Удаление пользователя.
     * Requirements: 3.3
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        
        // Нельзя удалить себя
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Нельзя удалить свой аккаунт.');
            return $this->redirect(['index']);
        }
        
        if ($this->userRepository->delete($model)) {
            Yii::$app->session->setFlash('success', 'Пользователь удалён.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить пользователя.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds model by ID using repository.
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): User
    {
        $model = $this->userRepository->findById($id);
        
        if ($model === null) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
        
        return $model;
    }
}

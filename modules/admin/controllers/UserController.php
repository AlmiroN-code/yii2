<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserController - управление пользователями в админке.
 * Requirements: 2.1-2.6
 */
class UserController extends Controller
{
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
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException('Доступ запрещён. Только для администраторов.');
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
     * Список пользователей.
     */
    public function actionIndex(): string
    {
        $role = Yii::$app->request->get('role');
        $status = Yii::$app->request->get('status');

        $query = User::find()->orderBy(['created_at' => SORT_DESC]);
        
        if ($role && \in_array($role, [User::ROLE_USER, User::ROLE_AUTHOR, User::ROLE_ADMIN], true)) {
            $query->andWhere(['role' => $role]);
        }
        
        if ($status !== null && $status !== '' && \in_array((int)$status, [User::STATUS_INACTIVE, User::STATUS_ACTIVE, User::STATUS_BANNED], true)) {
            $query->andWhere(['status' => (int)$status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentRole' => $role,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Редактирование пользователя.
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
        ]);
    }

    /**
     * Удаление пользователя.
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        
        // Нельзя удалить себя
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Нельзя удалить свой аккаунт.');
            return $this->redirect(['index']);
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Пользователь удалён.');
        return $this->redirect(['index']);
    }

    /**
     * Finds model by ID.
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): User
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Пользователь не найден.');
        }
        return $model;
    }
}

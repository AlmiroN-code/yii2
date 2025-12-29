<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;

/**
 * AuthController - контроллер авторизации.
 * Requirements: 1.1-1.9
 */
class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Login action.
     * Requirements: 1.1-1.4
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        // Редирект если уже авторизован
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register action.
     * Requirements: 1.5-1.7
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        // Редирект если уже авторизован
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            
            if ($user) {
                // Автоматический вход после регистрации
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Регистрация успешно завершена!');
                return $this->goHome();
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     * Requirements: 1.8
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        
        return $this->goHome();
    }
}

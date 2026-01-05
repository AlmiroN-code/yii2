<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\enums\PublicationStatus;
use app\services\SeoServiceInterface;

class SiteController extends Controller
{
    private SeoServiceInterface $seoService;

    public function __construct($id, $module, SeoServiceInterface $seoService, $config = [])
    {
        $this->seoService = $seoService;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage with latest publications.
     *
     * @return string
     */
    public function actionIndex()
    {
        $featuredCount = (int)\app\models\Setting::get('homepage_featured_count', '6');
        
        $publications = \app\models\Publication::find()
            ->where(['status' => PublicationStatus::PUBLISHED->value])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC])
            ->limit($featuredCount)
            ->all();

        // SEO: Schema.org WebSite
        Yii::$app->seo->setSchemaOrg($this->seoService->getWebsiteSchema());

        return $this->render('index', [
            'publications' => $publications,
            'settings' => [
                'title' => \app\models\Setting::get('homepage_title', ''),
                'subtitle' => \app\models\Setting::get('homepage_subtitle', ''),
                'hero_image' => \app\models\Setting::get('homepage_hero_image', ''),
                'show_categories' => \app\models\Setting::get('homepage_show_categories', '1') === '1',
                'show_tags' => \app\models\Setting::get('homepage_show_tags', '1') === '1',
            ],
        ]);
    }

    /**
     * Returns sitemap.xml content.
     * Requirements: 8.4
     */
    public function actionSitemap(): Response
    {
        $path = Yii::getAlias('@webroot/sitemap.xml');
        
        if (!file_exists($path)) {
            // Генерируем sitemap если не существует
            $this->seoService->generateSitemap();
        }

        $content = file_get_contents($path);
        
        return Yii::$app->response->sendContentAsFile(
            $content,
            'sitemap.xml',
            [
                'mimeType' => 'application/xml',
                'inline' => true,
            ]
        );
    }

    /**
     * Returns robots.txt content.
     * Requirements: 8.5
     */
    public function actionRobots(): Response
    {
        $content = $this->seoService->getRobotsContent();
        
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain');
        
        return Yii::$app->response->sendContentAsFile(
            $content,
            'robots.txt',
            [
                'mimeType' => 'text/plain',
                'inline' => true,
            ]
        );
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
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
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

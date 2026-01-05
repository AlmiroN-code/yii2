<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use Yii;
use app\models\Redirect;
use app\models\SeoSetting;
use app\models\WebmasterVerification;
use app\services\SeoServiceInterface;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * SeoController - управление SEO настройками в админке.
 * Requirements: 8.1-8.10
 */
class SeoController extends Controller
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
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => fn($rule, $action) => Yii::$app->user->identity?->isAdmin(),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'generate-sitemap' => ['post'],
                    'delete-redirect' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Глобальные SEO настройки.
     * Requirements: 8.1
     */
    public function actionIndex(): string|Response
    {
        $model = SeoSetting::getOrCreateGlobal();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('SeoSetting', []);
            
            // Обработка загрузки OG Image
            $ogImage = UploadedFile::getInstance($model, 'og_image');
            if ($ogImage) {
                $uploadPath = $this->uploadOgImage($ogImage);
                if ($uploadPath) {
                    $post['og_image'] = $uploadPath;
                }
            }

            if ($this->seoService->saveGlobalSettings($post)) {
                Yii::$app->session->setFlash('success', 'SEO настройки сохранены.');
                return $this->refresh();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Настройки и генерация Sitemap.
     * Requirements: 8.4
     */
    public function actionSitemap(): string
    {
        $settings = $this->seoService->getSitemapSettings();

        return $this->render('sitemap', [
            'settings' => $settings,
        ]);
    }

    /**
     * Генерация Sitemap.
     * Requirements: 8.4
     */
    public function actionGenerateSitemap(): Response
    {
        try {
            $path = $this->seoService->generateSitemap();
            Yii::$app->session->setFlash('success', 'Sitemap успешно сгенерирован: ' . basename($path));
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка генерации sitemap: ' . $e->getMessage());
            Yii::error('Sitemap generation error: ' . $e->getMessage(), __METHOD__);
        }

        return $this->redirect(['sitemap']);
    }

    /**
     * Редактирование robots.txt.
     * Requirements: 8.5
     */
    public function actionRobots(): string|Response
    {
        $content = $this->seoService->getRobotsContent();

        if (Yii::$app->request->isPost) {
            $newContent = Yii::$app->request->post('robots_content', '');
            
            // Простая валидация синтаксиса
            $hasWarning = $this->validateRobotsSyntax($newContent);
            
            if ($this->seoService->saveRobotsContent($newContent)) {
                Yii::$app->session->setFlash('success', 'robots.txt сохранён.');
                if ($hasWarning) {
                    Yii::$app->session->setFlash('warning', 'Обнаружены возможные проблемы с синтаксисом.');
                }
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка сохранения robots.txt');
            }
        }

        return $this->render('robots', [
            'content' => $content,
        ]);
    }


    /**
     * Список редиректов.
     * Requirements: 8.6
     */
    public function actionRedirects(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Redirect::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('redirects', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Создание редиректа.
     * Requirements: 8.6
     */
    public function actionCreateRedirect(): string|Response
    {
        $model = new Redirect();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Редирект создан.');
            return $this->redirect(['redirects']);
        }

        return $this->render('create-redirect', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование редиректа.
     * Requirements: 8.6
     */
    public function actionUpdateRedirect(int $id): string|Response
    {
        $model = $this->findRedirect($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Редирект обновлён.');
            return $this->redirect(['redirects']);
        }

        return $this->render('update-redirect', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление редиректа.
     * Requirements: 8.6
     */
    public function actionDeleteRedirect(int $id): Response
    {
        $model = $this->findRedirect($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Редирект удалён.');
        return $this->redirect(['redirects']);
    }

    /**
     * Настройки верификации вебмастеров.
     * Requirements: 8.9
     */
    public function actionWebmaster(): string|Response
    {
        $google = WebmasterVerification::getOrCreateForService(WebmasterVerification::SERVICE_GOOGLE);
        $yandex = WebmasterVerification::getOrCreateForService(WebmasterVerification::SERVICE_YANDEX);
        $bing = WebmasterVerification::getOrCreateForService(WebmasterVerification::SERVICE_BING);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            $this->saveWebmasterVerification($google, $post['google_code'] ?? '');
            $this->saveWebmasterVerification($yandex, $post['yandex_code'] ?? '');
            $this->saveWebmasterVerification($bing, $post['bing_code'] ?? '');

            Yii::$app->session->setFlash('success', 'Коды верификации сохранены.');
            return $this->refresh();
        }

        return $this->render('webmaster', [
            'google' => $google,
            'yandex' => $yandex,
            'bing' => $bing,
        ]);
    }

    /**
     * Сохраняет код верификации вебмастера.
     */
    private function saveWebmasterVerification(WebmasterVerification $model, string $code): void
    {
        if ($code === '') {
            if (!$model->isNewRecord) {
                $model->delete();
            }
            return;
        }

        $model->verification_code = $code;
        $model->is_active = true;
        $model->save();
    }

    /**
     * Находит редирект по ID.
     * 
     * @throws NotFoundHttpException
     */
    private function findRedirect(int $id): Redirect
    {
        $model = Redirect::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Редирект не найден.');
        }
        return $model;
    }

    /**
     * Загружает OG Image.
     */
    private function uploadOgImage(UploadedFile $file): ?string
    {
        $uploadDir = Yii::getAlias('@webroot/uploads/seo');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'og_' . time() . '.' . $file->extension;
        $filepath = $uploadDir . '/' . $filename;

        if ($file->saveAs($filepath)) {
            return '/uploads/seo/' . $filename;
        }
        return null;
    }

    /**
     * Валидирует синтаксис robots.txt.
     */
    private function validateRobotsSyntax(string $content): bool
    {
        $lines = explode("\n", $content);
        $hasWarning = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Проверяем базовые директивы
            $validDirectives = ['User-agent', 'Disallow', 'Allow', 'Sitemap', 'Crawl-delay', 'Host'];
            $isValid = false;
            foreach ($validDirectives as $directive) {
                if (stripos($line, $directive . ':') === 0 || stripos($line, $directive . ' ') === 0) {
                    $isValid = true;
                    break;
                }
            }

            if (!$isValid) {
                $hasWarning = true;
            }
        }

        return $hasWarning;
    }
}

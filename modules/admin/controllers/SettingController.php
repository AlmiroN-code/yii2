<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\models\Setting;

class SettingController extends Controller
{
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
            ],
        ];
    }

    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab', 'general');
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            
            if ($tab === 'general') {
                // Text settings
                Setting::set('site_name', $post['site_name'] ?? '');
                Setting::set('site_description', $post['site_description'] ?? '', 'textarea');
                Setting::set('site_keywords', $post['site_keywords'] ?? '');
                Setting::set('append_site_name', isset($post['append_site_name']) ? '1' : '0', 'checkbox');
                
                // Handle logo upload
                $logo = UploadedFile::getInstanceByName('site_logo');
                if ($logo) {
                    $logoPath = $this->uploadFile($logo, 'logo');
                    if ($logoPath) {
                        Setting::set('site_logo', $logoPath, 'image');
                    }
                }
                
                // Handle favicon upload
                $favicon = UploadedFile::getInstanceByName('site_favicon');
                if ($favicon) {
                    $faviconPath = $this->uploadFile($favicon, 'favicon');
                    if ($faviconPath) {
                        Setting::set('site_favicon', $faviconPath, 'image');
                    }
                }
            } elseif ($tab === 'homepage') {
                Setting::set('homepage_title', $post['homepage_title'] ?? '');
                Setting::set('homepage_subtitle', $post['homepage_subtitle'] ?? '');
                Setting::set('homepage_featured_count', $post['homepage_featured_count'] ?? '6');
                Setting::set('homepage_show_categories', isset($post['homepage_show_categories']) ? '1' : '0', 'checkbox');
                Setting::set('homepage_show_tags', isset($post['homepage_show_tags']) ? '1' : '0', 'checkbox');
                
                // Handle hero image upload
                $heroImage = UploadedFile::getInstanceByName('homepage_hero_image');
                if ($heroImage) {
                    $heroPath = $this->uploadFile($heroImage, 'hero');
                    if ($heroPath) {
                        Setting::set('homepage_hero_image', $heroPath, 'image');
                    }
                }
            }
            
            Yii::$app->session->setFlash('success', 'Настройки сохранены.');
            return $this->redirect(['index', 'tab' => $tab]);
        }

        return $this->render('index', [
            'tab' => $tab,
            'settings' => [
                'site_name' => Setting::get('site_name', ''),
                'site_description' => Setting::get('site_description', ''),
                'site_keywords' => Setting::get('site_keywords', ''),
                'site_logo' => Setting::get('site_logo', ''),
                'site_favicon' => Setting::get('site_favicon', ''),
                'append_site_name' => Setting::get('append_site_name', '0'),
                'homepage_title' => Setting::get('homepage_title', ''),
                'homepage_subtitle' => Setting::get('homepage_subtitle', ''),
                'homepage_featured_count' => Setting::get('homepage_featured_count', '6'),
                'homepage_show_categories' => Setting::get('homepage_show_categories', '1'),
                'homepage_show_tags' => Setting::get('homepage_show_tags', '1'),
                'homepage_hero_image' => Setting::get('homepage_hero_image', ''),
            ],
        ]);
    }

    private function uploadFile(UploadedFile $file, string $prefix): ?string
    {
        $uploadDir = Yii::getAlias('@webroot/uploads/settings');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $prefix . '_' . time() . '.' . $file->extension;
        $filepath = $uploadDir . '/' . $filename;

        if ($file->saveAs($filepath)) {
            return '/uploads/settings/' . $filename;
        }
        return null;
    }
}

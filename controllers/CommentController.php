<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Publication;
use app\models\CommentForm;

/**
 * CommentController - контроллер комментариев.
 * Requirements: 5.1-5.4
 */
class CommentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new comment.
     * Requirements: 5.1-5.4
     */
    public function actionCreate(int $publicationId)
    {
        $publication = Publication::findOne($publicationId);
        if (!$publication) {
            throw new NotFoundHttpException('Публикация не найдена.');
        }

        $model = new CommentForm($publicationId);

        if ($model->load(Yii::$app->request->post())) {
            $comment = $model->save();
            
            // AJAX response
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                if ($comment) {
                    return [
                        'success' => true,
                        'message' => 'Комментарий отправлен на модерацию.',
                    ];
                }
                
                return [
                    'success' => false,
                    'errors' => $model->errors,
                ];
            }

            // Regular form submission
            if ($comment) {
                Yii::$app->session->setFlash('success', 'Комментарий отправлен на модерацию.');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при отправке комментария.');
            }
        }

        return $this->redirect(['publication/view', 'slug' => $publication->slug]);
    }
}

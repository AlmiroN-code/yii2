<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Comment;

/**
 * CommentController - админ-контроллер модерации комментариев.
 * Requirements: 5.5, 5.6
 */
class CommentController extends Controller
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
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['post'],
                    'reject' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all comments.
     */
    public function actionIndex(): string
    {
        $status = Yii::$app->request->get('status');

        $query = Comment::find()->orderBy(['created_at' => SORT_DESC]);
        
        if ($status && in_array($status, [Comment::STATUS_PENDING, Comment::STATUS_APPROVED, Comment::STATUS_REJECTED, Comment::STATUS_SPAM])) {
            $query->where(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Approves a comment.
     */
    public function actionApprove(int $id)
    {
        $comment = $this->findModel($id);
        $comment->status = Comment::STATUS_APPROVED;
        $comment->save(false);

        Yii::$app->session->setFlash('success', 'Комментарий одобрен.');
        return $this->redirect(['index']);
    }

    /**
     * Rejects a comment.
     */
    public function actionReject(int $id)
    {
        $comment = $this->findModel($id);
        $comment->status = Comment::STATUS_REJECTED;
        $comment->save(false);

        Yii::$app->session->setFlash('success', 'Комментарий отклонён.');
        return $this->redirect(['index']);
    }

    /**
     * Deletes a comment.
     */
    public function actionDelete(int $id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', 'Комментарий удалён.');
        return $this->redirect(['index']);
    }

    /**
     * Finds model by ID.
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Comment
    {
        $model = Comment::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Комментарий не найден.');
        }
        return $model;
    }
}

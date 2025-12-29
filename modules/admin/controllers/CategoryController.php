<?php

namespace app\modules\admin\controllers;

use app\models\Category;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * CategoryController implements CRUD actions for Category model.
 * Requirements: 2.1, 5.3
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect(['/admin/auth/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find()->with(['parent', 'children']),
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'name' => SORT_ASC],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Category model.
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Категория успешно создана.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'parentCategories' => $this->getParentCategoryList(),
        ]);
    }

    /**
     * Updates an existing Category model.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Категория успешно обновлена.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'parentCategories' => $this->getParentCategoryList($model->id),
        ]);
    }

    /**
     * Deletes an existing Category model.
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Категория успешно удалена.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Категория не найдена.');
    }

    /**
     * Returns parent category list for dropdown.
     */
    protected function getParentCategoryList(?int $excludeId = null): array
    {
        $query = Category::find()->orderBy('name');
        
        if ($excludeId !== null) {
            $query->andWhere(['!=', 'id', $excludeId]);
        }

        return $query->select(['name', 'id'])->indexBy('id')->column();
    }
}

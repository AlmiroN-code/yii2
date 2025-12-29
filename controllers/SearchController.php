<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\services\SearchService;
use app\components\Breadcrumbs;

/**
 * SearchController - контроллер поиска.
 * Requirements: 7.1-7.6
 */
class SearchController extends Controller
{
    private $searchService;

    public function __construct($id, $module, $config = [])
    {
        $this->searchService = new SearchService();
        parent::__construct($id, $module, $config);
    }

    /**
     * Autocomplete API.
     * Requirements: 7.1, 7.2, 7.3
     */
    public function actionAutocomplete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Yii::$app->request->get('q', '');
        
        if (mb_strlen($query) < 2) {
            return [];
        }

        return $this->searchService->autocomplete($query);
    }

    /**
     * Search results page.
     * Requirements: 7.5
     */
    public function actionIndex(): string
    {
        $query = Yii::$app->request->get('q', '');
        
        $this->view->params['breadcrumbs'] = Breadcrumbs::forSearch($query);

        $dataProvider = null;
        if ($query) {
            $dataProvider = $this->searchService->search($query);
        }

        return $this->render('index', [
            'query' => $query,
            'dataProvider' => $dataProvider,
        ]);
    }
}

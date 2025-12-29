<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\services\SearchService;
use app\components\Breadcrumbs;

/**
 * SearchController - контроллер поиска.
 * Requirements: 2.1, 2.3, 2.4, 3.4
 */
class SearchController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly SearchService $searchService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Autocomplete API.
     * Requirements: 2.1, 2.3, 2.4
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
     * Requirements: 2.1
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

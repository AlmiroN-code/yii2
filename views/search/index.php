<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var string $query */
/** @var yii\data\ActiveDataProvider|null $dataProvider */

$this->title = $query ? 'Поиск: ' . $query : 'Поиск';
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        <?php if ($query): ?>
            Результаты поиска: "<?= Html::encode($query) ?>"
        <?php else: ?>
            Поиск
        <?php endif; ?>
    </h1>

    <!-- Search form -->
    <form action="<?= \yii\helpers\Url::to(['/search/index']) ?>" method="get" class="mb-8">
        <div class="flex gap-2">
            <input type="text" name="q" value="<?= Html::encode($query) ?>" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Введите запрос...">
            <?= Html::submitButton('Найти', [
                'class' => 'px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700',
            ]) ?>
        </div>
    </form>

    <?php if ($dataProvider): ?>
        <?php $models = $dataProvider->getModels(); ?>
        
        <?php if (empty($models)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500">По запросу "<?= Html::encode($query) ?>" ничего не найдено.</p>
            </div>
        <?php else: ?>
            <p class="text-gray-600 mb-4">
                Найдено результатов: <?= $dataProvider->getTotalCount() ?>
            </p>

            <div class="space-y-4">
                <?php foreach ($models as $publication): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-2">
                            <?= Html::a(Html::encode($publication->title), 
                                ['publication/view', 'slug' => $publication->slug],
                                ['class' => 'text-blue-600 hover:text-blue-800']) ?>
                        </h2>
                        <?php if ($publication->excerpt): ?>
                            <p class="text-gray-600 text-sm"><?= Html::encode($publication->excerpt) ?></p>
                        <?php endif; ?>
                        <div class="mt-2 text-xs text-gray-400">
                            <?= Yii::$app->formatter->asDate($publication->published_at) ?>
                            <?php if ($publication->category): ?>
                                • <?= Html::a($publication->category->name, 
                                    ['category/view', 'slug' => $publication->category->slug],
                                    ['class' => 'text-gray-500 hover:text-gray-700']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'flex justify-center gap-2'],
                    'linkOptions' => ['class' => 'px-3 py-1 border rounded hover:bg-gray-100'],
                    'activePageCssClass' => 'bg-blue-600 text-white',
                ]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\models\User;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $currentRole */
/** @var string|null $currentStatus */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                <select onchange="window.location.href='<?= Url::to(['index']) ?>?role=' + this.value + '&status=<?= $currentStatus ?>'" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Все роли</option>
                    <?php foreach (User::getRoleLabels() as $value => $label): ?>
                        <option value="<?= $value ?>" <?= $currentRole === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                <select onchange="window.location.href='<?= Url::to(['index']) ?>?role=<?= $currentRole ?>&status=' + this.value" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Все статусы</option>
                    <?php foreach (User::getStatusLabels() as $value => $label): ?>
                        <option value="<?= $value ?>" <?= $currentStatus !== null && (int)$currentStatus === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Роль</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Регистрация</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($dataProvider->getModels() as $user): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $user->id ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= Html::encode($user->username) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= Html::encode($user->email) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $roleColors = [
                                User::ROLE_USER => 'bg-gray-100 text-gray-800',
                                User::ROLE_AUTHOR => 'bg-blue-100 text-blue-800',
                                User::ROLE_ADMIN => 'bg-purple-100 text-purple-800',
                            ];
                            $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $roleColor ?>">
                                <?= $user->getRoleLabel() ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                User::STATUS_ACTIVE => 'bg-green-100 text-green-800',
                                User::STATUS_INACTIVE => 'bg-yellow-100 text-yellow-800',
                                User::STATUS_BANNED => 'bg-red-100 text-red-800',
                            ];
                            $statusColor = $statusColors[$user->status] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColor ?>">
                                <?= $user->getStatusLabel() ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= Yii::$app->formatter->asDate($user->created_at, 'short') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>', ['update', 'id' => $user->id], [
                                'class' => 'inline-flex text-blue-600 hover:text-blue-900 mr-3',
                                'title' => 'Редактировать',
                            ]) ?>
                            <?php if ($user->id !== Yii::$app->user->id): ?>
                                <?= Html::a('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>', ['delete', 'id' => $user->id], [
                                    'class' => 'inline-flex text-red-600 hover:text-red-900',
                                    'title' => 'Удалить',
                                    'data' => [
                                        'confirm' => 'Удалить пользователя ' . $user->username . '?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    <div class="mt-4">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->getPagination(),
            'options' => ['class' => 'flex justify-center gap-1'],
            'linkOptions' => ['class' => 'px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-gray-50'],
            'activePageCssClass' => 'bg-blue-500 text-white',
            'disabledPageCssClass' => 'opacity-50 cursor-not-allowed',
        ]) ?>
    </div>
</div>

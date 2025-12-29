<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CommentForm as CommentFormModel;

/**
 * CommentForm widget.
 * Requirements: 5.1, 5.3
 */
class CommentForm extends Widget
{
    public $publicationId;

    public function run(): string
    {
        $model = new CommentFormModel($this->publicationId);
        $isGuest = Yii::$app->user->isGuest;

        ob_start();
        ?>
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Оставить отзыв</h3>
            
            <?php $form = ActiveForm::begin([
                'action' => ['/comment/create', 'publicationId' => $this->publicationId],
                'options' => ['class' => 'space-y-4'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'block text-sm font-medium text-gray-700 mb-1'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ],
            ]); ?>

            <?php if ($isGuest): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?= $form->field($model, 'name')->textInput([
                        'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
                        'placeholder' => 'Ваше имя',
                    ]) ?>
                    <?= $form->field($model, 'email')->textInput([
                        'type' => 'email',
                        'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
                        'placeholder' => 'email@example.com',
                    ]) ?>
                </div>
            <?php endif; ?>

            <!-- Rating -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Оценка</label>
                <div class="flex gap-1 rating-stars" data-rating="5">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="star-btn text-2xl <?= $i <= 5 ? 'text-yellow-400' : 'text-gray-300' ?>" data-value="<?= $i ?>">★</button>
                    <?php endfor; ?>
                </div>
                <?= $form->field($model, 'rating', ['template' => '{input}'])->hiddenInput(['class' => 'rating-input']) ?>
            </div>

            <?= $form->field($model, 'content')->textarea([
                'class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
                'rows' => 4,
                'placeholder' => 'Напишите ваш отзыв...',
            ]) ?>

            <!-- Honeypot (hidden) -->
            <div style="position: absolute; left: -9999px;">
                <?= $form->field($model, 'honeypot', ['template' => '{input}'])->textInput(['tabindex' => '-1', 'autocomplete' => 'off']) ?>
            </div>

            <div>
                <?= Html::submitButton('Отправить отзыв', [
                    'class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <script>
        document.querySelectorAll('.rating-stars').forEach(container => {
            const input = container.parentElement.querySelector('.rating-input');
            const stars = container.querySelectorAll('.star-btn');
            
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.dataset.value);
                    input.value = value;
                    stars.forEach((s, i) => {
                        s.classList.toggle('text-yellow-400', i < value);
                        s.classList.toggle('text-gray-300', i >= value);
                    });
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

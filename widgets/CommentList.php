<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\models\Comment;

/**
 * CommentList widget.
 * Requirements: 5.5
 */
class CommentList extends Widget
{
    public $publicationId;

    public function run(): string
    {
        $comments = Comment::findApproved()
            ->where(['publication_id' => $this->publicationId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $avgRating = Comment::getAverageRating($this->publicationId);

        ob_start();
        ?>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    Отзывы (<?= count($comments) ?>)
                </h3>
                <?php if ($avgRating): ?>
                    <div class="flex items-center gap-1">
                        <span class="text-yellow-400 text-xl">★</span>
                        <span class="font-semibold"><?= $avgRating ?></span>
                        <span class="text-gray-500 text-sm">/ 5</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($comments)): ?>
                <p class="text-gray-500 text-center py-8">Пока нет отзывов. Будьте первым!</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($comments as $comment): ?>
                        <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-start gap-4">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <?php if ($comment->user && $comment->user->profile && $comment->user->profile->avatar): ?>
                                        <img src="<?= Html::encode($comment->user->profile->getAvatarUrl()) ?>" 
                                             class="w-10 h-10 rounded-full object-cover" alt="">
                                    <?php elseif ($comment->user && $comment->user->profile): ?>
                                        <img src="<?= Yii::getAlias('@web/images/default-avatar.svg') ?>" 
                                             class="w-10 h-10 rounded-full" alt="">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 font-medium text-sm">
                                                <?= strtoupper(mb_substr($comment->getAuthorName(), 0, 1)) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">
                                            <?= Html::encode($comment->getAuthorName()) ?>
                                        </span>
                                        <span class="text-gray-400 text-sm">•</span>
                                        <span class="text-gray-500 text-sm">
                                            <?= Yii::$app->formatter->asRelativeTime($comment->created_at) ?>
                                        </span>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex gap-0.5 mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="text-sm <?= $i <= $comment->rating ? 'text-yellow-400' : 'text-gray-300' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>

                                    <p class="text-gray-700"><?= Html::encode($comment->content) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

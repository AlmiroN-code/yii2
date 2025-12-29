<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\models\Favorite;

/**
 * FavoriteButton widget.
 * Requirements: 3.1, 3.2, 3.4
 */
class FavoriteButton extends Widget
{
    public $publicationId;
    public $showCount = true;

    private $isFavorite = false;
    private $count = 0;

    public function init()
    {
        parent::init();
        
        if (!Yii::$app->user->isGuest) {
            $this->isFavorite = Favorite::isFavorite(Yii::$app->user->id, $this->publicationId);
        }
        $this->count = Favorite::getCount($this->publicationId);
    }

    public function run(): string
    {
        $this->registerJs();

        $buttonClass = $this->isFavorite 
            ? 'favorite-btn active text-red-500 hover:text-red-600' 
            : 'favorite-btn text-gray-400 hover:text-red-500';

        $icon = $this->isFavorite ? $this->getFilledHeartIcon() : $this->getOutlineHeartIcon();

        $html = Html::beginTag('button', [
            'type' => 'button',
            'class' => $buttonClass . ' inline-flex items-center gap-1 transition-colors',
            'data-publication-id' => $this->publicationId,
            'data-favorite' => $this->isFavorite ? '1' : '0',
            'title' => $this->isFavorite ? 'Удалить из избранного' : 'Добавить в избранное',
        ]);

        $html .= '<span class="favorite-icon">' . $icon . '</span>';
        
        if ($this->showCount) {
            $html .= '<span class="favorite-count text-sm">' . $this->count . '</span>';
        }

        $html .= Html::endTag('button');

        return $html;
    }

    private function registerJs(): void
    {
        $js = <<<JS
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.favorite-btn');
    if (!btn) return;
    
    const publicationId = btn.dataset.publicationId;
    
    fetch('/api/favorite/toggle/' + publicationId, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
    })
    .then(r => r.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
            return;
        }
        if (data.success) {
            btn.dataset.favorite = data.isFavorite ? '1' : '0';
            btn.classList.toggle('active', data.isFavorite);
            btn.classList.toggle('text-red-500', data.isFavorite);
            btn.classList.toggle('text-gray-400', !data.isFavorite);
            
            const icon = btn.querySelector('.favorite-icon');
            icon.innerHTML = data.isFavorite 
                ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>'
                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>';
            
            const count = btn.querySelector('.favorite-count');
            if (count) count.textContent = data.count;
        }
    });
});
JS;
        Yii::$app->view->registerJs($js, \yii\web\View::POS_END, 'favorite-button');
    }

    private function getFilledHeartIcon(): string
    {
        return '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>';
    }

    private function getOutlineHeartIcon(): string
    {
        return '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>';
    }
}

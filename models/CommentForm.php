<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * CommentForm - форма комментария.
 * Requirements: 5.1, 5.2, 5.3
 */
class CommentForm extends Model
{
    public $name;
    public $email;
    public $content;
    public $rating = 5;
    public $honeypot; // Антиспам поле

    private $_publicationId;

    public function __construct(int $publicationId, $config = [])
    {
        $this->_publicationId = $publicationId;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            // Honeypot должен быть пустым
            [['honeypot'], 'validateHoneypot'],
            
            // Для гостей обязательны name и email
            [['name', 'email'], 'required', 'when' => function() {
                return Yii::$app->user->isGuest;
            }, 'whenClient' => "function() { return true; }"],
            
            [['content'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['content'], 'string', 'min' => 3, 'max' => 5000],
            [['rating'], 'integer'],
            [['rating'], 'in', 'range' => [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Ваше имя',
            'email' => 'Email',
            'content' => 'Комментарий',
            'rating' => 'Оценка',
        ];
    }

    /**
     * Validates honeypot field (must be empty).
     */
    public function validateHoneypot($attribute, $params): void
    {
        if (!empty($this->honeypot)) {
            // Тихо отклоняем как спам
            $this->addError($attribute, '');
        }
    }

    /**
     * Saves comment.
     */
    public function save(): ?Comment
    {
        if (!$this->validate()) {
            return null;
        }

        $comment = new Comment();
        $comment->publication_id = $this->_publicationId;
        $comment->content = $this->content;
        $comment->rating = $this->rating;
        $comment->ip_address = Yii::$app->request->userIP;

        // Автозаполнение для авторизованных
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $comment->user_id = $user->id;
            $comment->guest_name = null;
            $comment->guest_email = null;
        } else {
            $comment->guest_name = $this->name;
            $comment->guest_email = $this->email;
        }

        // Проверка на спам
        if ($comment->isSpam()) {
            $comment->status = Comment::STATUS_SPAM;
        } else {
            $comment->status = Comment::STATUS_PENDING;
        }

        if ($comment->save()) {
            return $comment;
        }

        return null;
    }
}

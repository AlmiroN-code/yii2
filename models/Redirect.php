<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель для хранения 301/302 редиректов.
 * Requirements: 8.6
 *
 * @property int $id
 * @property string $source_url
 * @property string $target_url
 * @property int $type
 * @property int $hits
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Redirect extends ActiveRecord
{
    public const TYPE_PERMANENT = 301;
    public const TYPE_TEMPORARY = 302;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%redirect}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['source_url', 'target_url'], 'required'],
            [['source_url', 'target_url'], 'string', 'max' => 500],
            [['source_url'], 'unique', 'message' => 'Редирект с таким исходным URL уже существует'],
            [['source_url'], 'validateNotSameAsTarget'],
            [['source_url'], 'validateNoCycle'],
            [['type'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_PERMANENT, self::TYPE_TEMPORARY]],
            [['type'], 'default', 'value' => self::TYPE_PERMANENT],
            [['hits'], 'integer'],
            [['hits'], 'default', 'value' => 0],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    /**
     * Валидация: source_url не должен совпадать с target_url.
     */
    public function validateNotSameAsTarget(string $attribute): void
    {
        if ($this->source_url === $this->target_url) {
            $this->addError($attribute, 'Исходный и целевой URL не могут совпадать');
        }
    }

    /**
     * Валидация: проверка на циклические редиректы.
     */
    public function validateNoCycle(string $attribute): void
    {
        $visited = [$this->source_url];
        $current = $this->target_url;
        $maxDepth = 10;
        $depth = 0;

        while ($depth < $maxDepth) {
            if (in_array($current, $visited, true)) {
                $this->addError($attribute, 'Обнаружен циклический редирект');
                return;
            }

            $nextRedirect = static::findBySourceUrl($current);
            if ($nextRedirect === null) {
                break;
            }

            $visited[] = $current;
            $current = $nextRedirect->target_url;
            $depth++;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'source_url' => 'Исходный URL',
            'target_url' => 'Целевой URL',
            'type' => 'Тип редиректа',
            'hits' => 'Переходы',
            'is_active' => 'Активен',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Находит редирект по исходному URL.
     */
    public static function findBySourceUrl(string $sourceUrl): ?self
    {
        return static::findOne([
            'source_url' => $sourceUrl,
            'is_active' => true,
        ]);
    }

    /**
     * Увеличивает счётчик переходов.
     */
    public function incrementHits(): bool
    {
        return $this->updateCounters(['hits' => 1]);
    }

    /**
     * Возвращает список типов редиректов.
     * 
     * @return array<int, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PERMANENT => '301 (Постоянный)',
            self::TYPE_TEMPORARY => '302 (Временный)',
        ];
    }

    /**
     * Возвращает метку типа редиректа.
     */
    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? (string)$this->type;
    }
}

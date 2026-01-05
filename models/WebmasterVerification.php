<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель для хранения кодов верификации вебмастер-сервисов.
 * Requirements: 8.9
 *
 * @property int $id
 * @property string $service
 * @property string $verification_code
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class WebmasterVerification extends ActiveRecord
{
    public const SERVICE_GOOGLE = 'google';
    public const SERVICE_YANDEX = 'yandex';
    public const SERVICE_BING = 'bing';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%webmaster_verification}}';
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
            [['service', 'verification_code'], 'required'],
            [['service'], 'in', 'range' => [self::SERVICE_GOOGLE, self::SERVICE_YANDEX, self::SERVICE_BING]],
            [['service'], 'unique', 'message' => 'Код верификации для этого сервиса уже существует'],
            [['verification_code'], 'string', 'max' => 255],
            [['verification_code'], 'trim'],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'service' => 'Сервис',
            'verification_code' => 'Код верификации',
            'is_active' => 'Активен',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Возвращает список сервисов.
     * 
     * @return array<string, string>
     */
    public static function getServices(): array
    {
        return [
            self::SERVICE_GOOGLE => 'Google Search Console',
            self::SERVICE_YANDEX => 'Яндекс.Вебмастер',
            self::SERVICE_BING => 'Bing Webmaster Tools',
        ];
    }

    /**
     * Возвращает метку сервиса.
     */
    public function getServiceLabel(): string
    {
        return self::getServices()[$this->service] ?? $this->service;
    }

    /**
     * Возвращает имя мета-тега для сервиса.
     */
    public function getMetaTagName(): string
    {
        switch ($this->service) {
            case self::SERVICE_GOOGLE:
                return 'google-site-verification';
            case self::SERVICE_YANDEX:
                return 'yandex-verification';
            case self::SERVICE_BING:
                return 'msvalidate.01';
            default:
                return '';
        }
    }

    /**
     * Находит верификацию по сервису.
     */
    public static function findByService(string $service): ?self
    {
        return static::findOne([
            'service' => $service,
            'is_active' => true,
        ]);
    }

    /**
     * Получает все активные верификации.
     * 
     * @return self[]
     */
    public static function findAllActive(): array
    {
        return static::find()
            ->where(['is_active' => true])
            ->all();
    }

    /**
     * Получает или создаёт верификацию для сервиса.
     */
    public static function getOrCreateForService(string $service): self
    {
        $verification = static::findOne(['service' => $service]);
        if ($verification === null) {
            $verification = new self([
                'service' => $service,
                'is_active' => true,
            ]);
        }
        return $verification;
    }
}

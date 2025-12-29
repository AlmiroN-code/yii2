<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Setting extends ActiveRecord
{
    private static $_cache = [];

    public static function tableName()
    {
        return '{{%setting}}';
    }

    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key'], 'string', 'max' => 100],
            [['value'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'key' => 'Ключ',
            'value' => 'Значение',
            'type' => 'Тип',
        ];
    }

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        if (!isset(self::$_cache[$key])) {
            $setting = self::findOne(['key' => $key]);
            self::$_cache[$key] = $setting ? $setting->value : null;
        }
        return self::$_cache[$key] !== null ? self::$_cache[$key] : $default;
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value, string $type = 'text'): bool
    {
        $setting = self::findOne(['key' => $key]);
        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
            $setting->type = $type;
        }
        $setting->value = $value;
        self::$_cache[$key] = $value;
        return $setting->save();
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        self::$_cache = [];
    }
}

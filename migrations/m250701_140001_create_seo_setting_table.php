<?php

use yii\db\Migration;

/**
 * Создание таблицы seo_setting для хранения SEO настроек.
 * Requirements: 8.1, 8.2, 8.8
 */
class m250701_140001_create_seo_setting_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%seo_setting}}', [
            'id' => $this->primaryKey(),
            'entity_type' => "ENUM('global', 'publication', 'category', 'page') NOT NULL",
            'entity_id' => $this->integer()->defaultValue(null),
            'meta_title' => $this->string(255)->defaultValue(null),
            'meta_description' => $this->text()->defaultValue(null),
            'meta_keywords' => $this->string(500)->defaultValue(null),
            'og_title' => $this->string(255)->defaultValue(null),
            'og_description' => $this->text()->defaultValue(null),
            'og_image' => $this->string(500)->defaultValue(null),
            'canonical_url' => $this->string(500)->defaultValue(null),
            'robots' => $this->string(50)->defaultValue('index,follow'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Уникальный индекс по entity_type + entity_id
        $this->createIndex(
            'idx_seo_setting_entity',
            '{{%seo_setting}}',
            ['entity_type', 'entity_id'],
            true
        );

        // Индекс по entity_type для быстрого поиска
        $this->createIndex(
            'idx_seo_setting_entity_type',
            '{{%seo_setting}}',
            'entity_type'
        );

        // Создаём глобальную запись по умолчанию
        $this->insert('{{%seo_setting}}', [
            'entity_type' => 'global',
            'entity_id' => null,
            'meta_title' => 'Yii2 Blog',
            'meta_description' => 'Блог-платформа на Yii2',
            'robots' => 'index,follow',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%seo_setting}}');
    }
}

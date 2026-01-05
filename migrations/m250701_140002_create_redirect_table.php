<?php

use yii\db\Migration;

/**
 * Создание таблицы redirect для хранения 301/302 редиректов.
 * Requirements: 8.3, 8.6
 */
class m250701_140002_create_redirect_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%redirect}}', [
            'id' => $this->primaryKey(),
            'source_url' => $this->string(500)->notNull(),
            'target_url' => $this->string(500)->notNull(),
            'type' => $this->smallInteger()->defaultValue(301),
            'hits' => $this->integer()->defaultValue(0),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Уникальный индекс по source_url
        $this->createIndex(
            'idx_redirect_source_url',
            '{{%redirect}}',
            'source_url',
            true
        );

        // Индекс по is_active для быстрого поиска активных редиректов
        $this->createIndex(
            'idx_redirect_is_active',
            '{{%redirect}}',
            'is_active'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%redirect}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Создание таблицы webmaster_verification для хранения кодов верификации.
 * Requirements: 8.9
 */
class m250701_140003_create_webmaster_verification_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%webmaster_verification}}', [
            'id' => $this->primaryKey(),
            'service' => "ENUM('google', 'yandex', 'bing') NOT NULL",
            'verification_code' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Уникальный индекс по service
        $this->createIndex(
            'idx_webmaster_verification_service',
            '{{%webmaster_verification}}',
            'service',
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%webmaster_verification}}');
    }
}

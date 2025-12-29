<?php

use yii\db\Migration;

/**
 * Добавление роли пользователя.
 * Requirements: 1.1, 1.2, 1.3
 */
class m250701_120000_add_role_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->string(20)->notNull()->defaultValue('user'));
        $this->createIndex('idx-user-role', '{{%user}}', 'role');
        
        // Назначаем первому пользователю роль admin
        $this->update('{{%user}}', ['role' => 'admin'], ['id' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-role', '{{%user}}');
        $this->dropColumn('{{%user}}', 'role');
    }
}

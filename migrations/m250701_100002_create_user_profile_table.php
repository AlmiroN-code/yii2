<?php

use yii\db\Migration;

/**
 * Создание таблицы user_profile.
 * Requirements: 2.1-2.7
 */
class m250701_100002_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'display_name' => $this->string(100)->null(),
            'avatar' => $this->string(255)->null(),
            'bio' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-user_profile-user_id',
            '{{%user_profile}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_profile-user_id', '{{%user_profile}}');
        $this->dropTable('{{%user_profile}}');
    }
}

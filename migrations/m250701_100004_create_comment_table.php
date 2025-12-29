<?php

use yii\db\Migration;

/**
 * Создание таблицы comment.
 * Requirements: 5.1-5.7
 */
class m250701_100004_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'publication_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->null(),
            'guest_name' => $this->string(100)->null(),
            'guest_email' => $this->string(255)->null(),
            'content' => $this->text()->notNull(),
            'rating' => $this->tinyInteger()->notNull()->defaultValue(5),
            'status' => "ENUM('pending', 'approved', 'rejected', 'spam') NOT NULL DEFAULT 'pending'",
            'ip_address' => $this->string(45)->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Indexes
        $this->createIndex('idx-comment-publication_status', '{{%comment}}', ['publication_id', 'status']);
        $this->createIndex('idx-comment-status', '{{%comment}}', 'status');

        // Foreign keys
        $this->addForeignKey(
            'fk-comment-publication_id',
            '{{%comment}}',
            'publication_id',
            '{{%publication}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-comment-user_id',
            '{{%comment}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comment-user_id', '{{%comment}}');
        $this->dropForeignKey('fk-comment-publication_id', '{{%comment}}');
        $this->dropTable('{{%comment}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Создание таблицы favorite.
 * Requirements: 3.1-3.5
 */
class m250701_100003_create_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%favorite}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'publication_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Unique constraint
        $this->createIndex(
            'idx-favorite-user_publication',
            '{{%favorite}}',
            ['user_id', 'publication_id'],
            true
        );

        // Foreign keys
        $this->addForeignKey(
            'fk-favorite-user_id',
            '{{%favorite}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-favorite-publication_id',
            '{{%favorite}}',
            'publication_id',
            '{{%publication}}',
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
        $this->dropForeignKey('fk-favorite-publication_id', '{{%favorite}}');
        $this->dropForeignKey('fk-favorite-user_id', '{{%favorite}}');
        $this->dropTable('{{%favorite}}');
    }
}

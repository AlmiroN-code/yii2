<?php

use yii\db\Migration;

/**
 * Добавление автора к публикациям.
 */
class m250701_100005_add_author_to_publication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%publication}}', 'author_id', $this->integer()->null()->after('category_id'));
        
        $this->addForeignKey(
            'fk-publication-author_id',
            '{{%publication}}',
            'author_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('idx-publication-author_id', '{{%publication}}', 'author_id');

        // Назначить admin автором всех существующих публикаций
        $this->update('{{%publication}}', ['author_id' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-publication-author_id', '{{%publication}}');
        $this->dropIndex('idx-publication-author_id', '{{%publication}}');
        $this->dropColumn('{{%publication}}', 'author_id');
    }
}

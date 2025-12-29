<?php

use yii\db\Migration;

/**
 * Creates the `publication_tag` junction table.
 * Requirements: 3.2
 */
class m250101_000004_create_publication_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%publication_tag}}', [
            'publication_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ]);

        // Composite primary key
        $this->addPrimaryKey(
            'pk-publication_tag',
            '{{%publication_tag}}',
            ['publication_id', 'tag_id']
        );

        // Foreign key to publication (CASCADE delete)
        $this->addForeignKey(
            'fk-publication_tag-publication_id',
            '{{%publication_tag}}',
            'publication_id',
            '{{%publication}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Foreign key to tag (CASCADE delete)
        $this->addForeignKey(
            'fk-publication_tag-tag_id',
            '{{%publication_tag}}',
            'tag_id',
            '{{%tag}}',
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
        $this->dropForeignKey('fk-publication_tag-tag_id', '{{%publication_tag}}');
        $this->dropForeignKey('fk-publication_tag-publication_id', '{{%publication_tag}}');
        $this->dropTable('{{%publication_tag}}');
    }
}

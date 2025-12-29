<?php

use yii\db\Migration;

/**
 * Creates the `category` table for hierarchical categories.
 * Requirements: 2.1
 */
class m250101_000001_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'description' => $this->text()->null(),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Self-referencing foreign key for parent category
        $this->addForeignKey(
            'fk-category-parent_id',
            '{{%category}}',
            'parent_id',
            '{{%category}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Index for faster parent lookups
        $this->createIndex('idx-category-parent_id', '{{%category}}', 'parent_id');
        $this->createIndex('idx-category-slug', '{{%category}}', 'slug');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-category-parent_id', '{{%category}}');
        $this->dropTable('{{%category}}');
    }
}

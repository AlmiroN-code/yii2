<?php

use yii\db\Migration;

/**
 * Creates the `publication` table.
 * Requirements: 1.1
 */
class m250101_000003_create_publication_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%publication}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->null(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'excerpt' => $this->text()->null(),
            'content' => $this->getDb()->getDriverName() === 'mysql' ? 'LONGTEXT NOT NULL' : $this->text()->notNull(),
            'featured_image' => $this->string(255)->null(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'meta_title' => $this->string(255)->null(),
            'meta_description' => $this->text()->null(),
            'views' => $this->integer()->defaultValue(0),
            'published_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Foreign key to category (SET NULL on delete per requirement 2.3)
        $this->addForeignKey(
            'fk-publication-category_id',
            '{{%publication}}',
            'category_id',
            '{{%category}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Indexes
        $this->createIndex('idx-publication-slug', '{{%publication}}', 'slug', true);
        $this->createIndex('idx-publication-category_id', '{{%publication}}', 'category_id');
        $this->createIndex('idx-publication-status', '{{%publication}}', 'status');
        $this->createIndex('idx-publication-published_at', '{{%publication}}', 'published_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-publication-category_id', '{{%publication}}');
        $this->dropTable('{{%publication}}');
    }
}

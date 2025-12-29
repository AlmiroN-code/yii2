<?php

use yii\db\Migration;

/**
 * Creates the `tag` table.
 * Requirements: 3.1
 */
class m250101_000002_create_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Unique index on slug for SEO-friendly URLs
        $this->createIndex('idx-tag-slug', '{{%tag}}', 'slug', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tag}}');
    }
}

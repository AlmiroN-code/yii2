<?php

use yii\db\Migration;

/**
 * Создаёт таблицу статических страниц.
 */
class m250701_150001_create_page_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text()->notNull(),
            'meta_title' => $this->string(255)->null(),
            'meta_description' => $this->text()->null(),
            'is_active' => $this->boolean()->defaultValue(true),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-page-slug', '{{%page}}', 'slug');
        $this->createIndex('idx-page-is_active', '{{%page}}', 'is_active');
    }

    public function safeDown()
    {
        $this->dropTable('{{%page}}');
    }
}

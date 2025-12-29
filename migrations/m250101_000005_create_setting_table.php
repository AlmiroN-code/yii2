<?php

use yii\db\Migration;

class m250101_000005_create_setting_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(100)->notNull()->unique(),
            'value' => $this->text(),
            'type' => $this->string(20)->defaultValue('text'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Insert default settings
        $this->batchInsert('{{%setting}}', ['key', 'value', 'type'], [
            ['site_name', 'My Blog', 'text'],
            ['site_description', '', 'textarea'],
            ['site_keywords', '', 'text'],
            ['site_logo', '', 'image'],
            ['site_favicon', '', 'image'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
    }
}

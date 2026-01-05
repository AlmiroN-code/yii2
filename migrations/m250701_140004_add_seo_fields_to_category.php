<?php

use yii\db\Migration;

/**
 * Добавляет SEO поля в таблицу category.
 * Requirements: 8.2
 */
class m250701_140004_add_seo_fields_to_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category}}', 'meta_title', $this->string(255)->null()->after('description'));
        $this->addColumn('{{%category}}', 'meta_description', $this->text()->null()->after('meta_title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%category}}', 'meta_description');
        $this->dropColumn('{{%category}}', 'meta_title');
    }
}

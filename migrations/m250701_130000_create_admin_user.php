<?php

use yii\db\Migration;

/**
 * Создание администратора по умолчанию
 */
class m250701_130000_create_admin_user extends Migration
{
    public function safeUp()
    {
        // Проверяем, нет ли уже админа
        $adminExists = (new \yii\db\Query())
            ->from('{{%user}}')
            ->where(['role' => 'admin'])
            ->exists();

        if (!$adminExists) {
            $this->insert('{{%user}}', [
                'username' => 'admin',
                'email' => 'admin@wizai.ru',
                'password_hash' => Yii::$app->security->generatePasswordHash('Admin123!'),
                'auth_key' => Yii::$app->security->generateRandomString(),
                'role' => 'admin',
                'status' => 10, // STATUS_ACTIVE
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('{{%user}}', ['username' => 'admin', 'email' => 'admin@wizai.ru']);
    }
}

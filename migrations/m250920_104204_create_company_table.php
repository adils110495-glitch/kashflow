<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m250920_104204_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'short_name' => $this->string(100),
            'email' => $this->string(255),
            'phone_no' => $this->string(20),
            'logo' => $this->string(500),
            'website_link' => $this->string(500),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');
    }
}

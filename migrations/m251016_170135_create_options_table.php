<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%options}}`.
 */
class m251016_170135_create_options_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%options}}', [
            'id' => $this->primaryKey(),
            'key_name' => $this->string(255)->notNull()->comment('Option key name'),
            'key_value' => $this->text()->comment('Option value'),
            'created_at' => $this->integer()->notNull()->comment('Created timestamp'),
            'updated_at' => $this->integer()->notNull()->comment('Updated timestamp'),
        ]);

        // Add unique index on key_name
        $this->createIndex('idx_options_key_name', '{{%options}}', 'key_name', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%options}}');
    }
}

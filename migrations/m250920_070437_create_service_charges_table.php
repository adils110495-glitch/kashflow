<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_charges}}`.
 */
class m250920_070437_create_service_charges_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service_charges}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'rate' => $this->decimal(10, 2)->notNull(),
            'status' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service_charges}}');
    }
}

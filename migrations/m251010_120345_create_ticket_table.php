<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket}}`.
 */
class m251010_120345_create_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'subject' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('open'),
            'priority' => $this->string(20)->notNull()->defaultValue('medium'),
            'admin_response' => $this->text()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key constraint
        $this->addForeignKey(
            'fk-ticket-customer_id',
            '{{%ticket}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex(
            'idx-ticket-customer_id',
            '{{%ticket}}',
            'customer_id'
        );

        $this->createIndex(
            'idx-ticket-status',
            '{{%ticket}}',
            'status'
        );

        $this->createIndex(
            'idx-ticket-priority',
            '{{%ticket}}',
            'priority'
        );

        $this->createIndex(
            'idx-ticket-created_at',
            '{{%ticket}}',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign key constraint
        $this->dropForeignKey(
            'fk-ticket-customer_id',
            '{{%ticket}}'
        );

        // Drop indexes
        $this->dropIndex(
            'idx-ticket-customer_id',
            '{{%ticket}}'
        );

        $this->dropIndex(
            'idx-ticket-status',
            '{{%ticket}}'
        );

        $this->dropIndex(
            'idx-ticket-priority',
            '{{%ticket}}'
        );

        $this->dropIndex(
            'idx-ticket-created_at',
            '{{%ticket}}'
        );

        // Drop table
        $this->dropTable('{{%ticket}}');
    }
}

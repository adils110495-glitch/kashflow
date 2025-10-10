<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ledger}}`.
 */
class m250930_030000_create_ledger_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ledger}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull()->comment('Customer ID'),
            'date' => $this->date()->notNull()->comment('Transaction Date'),
            'debit' => $this->decimal(15, 2)->defaultValue(0)->comment('Debit Amount'),
            'credit' => $this->decimal(15, 2)->defaultValue(0)->comment('Credit Amount'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('Transaction Type: 1=Topup, 2=Withdrawal, 3=Topup Refund, 4=Withdrawal Refund'),
            'status' => $this->integer()->notNull()->defaultValue(1)->comment('Status: 0=Inactive, 1=Active'),
            'action_by' => $this->integer()->notNull()->comment('User who performed the action'),
            'action_date_time' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Action Date and Time'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key constraints
        $this->addForeignKey(
            'fk-ledger-customer_id',
            '{{%ledger}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ledger-action_by',
            '{{%ledger}}',
            'action_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex('idx-ledger-customer_id', '{{%ledger}}', 'customer_id');
        $this->createIndex('idx-ledger-date', '{{%ledger}}', 'date');
        $this->createIndex('idx-ledger-type', '{{%ledger}}', 'type');
        $this->createIndex('idx-ledger-status', '{{%ledger}}', 'status');
        $this->createIndex('idx-ledger-action_by', '{{%ledger}}', 'action_by');
        $this->createIndex('idx-ledger-action_date_time', '{{%ledger}}', 'action_date_time');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-ledger-customer_id', '{{%ledger}}');
        $this->dropForeignKey('fk-ledger-action_by', '{{%ledger}}');
        
        // Drop indexes
        $this->dropIndex('idx-ledger-customer_id', '{{%ledger}}');
        $this->dropIndex('idx-ledger-date', '{{%ledger}}');
        $this->dropIndex('idx-ledger-type', '{{%ledger}}');
        $this->dropIndex('idx-ledger-status', '{{%ledger}}');
        $this->dropIndex('idx-ledger-action_by', '{{%ledger}}');
        $this->dropIndex('idx-ledger-action_date_time', '{{%ledger}}');
        
        // Drop table
        $this->dropTable('{{%ledger}}');
    }
}

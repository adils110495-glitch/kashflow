<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%withdrawal}}`.
 */
class m250930_040000_create_withdrawal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%withdrawal}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull()->comment('Customer ID'),
            'date' => $this->date()->notNull()->comment('Withdrawal Date'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('Status: 0=Pending, 1=Approved, 2=Rejected, 3=Processing, 4=Completed'),
            'comment' => $this->text()->comment('Comment/Notes'),
            'action_by' => $this->integer()->notNull()->comment('User who performed the action'),
            'action_date_time' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Action Date and Time'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key constraints
        $this->addForeignKey(
            'fk-withdrawal-customer_id',
            '{{%withdrawal}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-withdrawal-action_by',
            '{{%withdrawal}}',
            'action_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Add indexes for better performance
        $this->createIndex('idx-withdrawal-customer_id', '{{%withdrawal}}', 'customer_id');
        $this->createIndex('idx-withdrawal-date', '{{%withdrawal}}', 'date');
        $this->createIndex('idx-withdrawal-status', '{{%withdrawal}}', 'status');
        $this->createIndex('idx-withdrawal-action_by', '{{%withdrawal}}', 'action_by');
        $this->createIndex('idx-withdrawal-action_date_time', '{{%withdrawal}}', 'action_date_time');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-withdrawal-customer_id', '{{%withdrawal}}');
        $this->dropForeignKey('fk-withdrawal-action_by', '{{%withdrawal}}');
        
        // Drop indexes
        $this->dropIndex('idx-withdrawal-customer_id', '{{%withdrawal}}');
        $this->dropIndex('idx-withdrawal-date', '{{%withdrawal}}');
        $this->dropIndex('idx-withdrawal-status', '{{%withdrawal}}');
        $this->dropIndex('idx-withdrawal-action_by', '{{%withdrawal}}');
        $this->dropIndex('idx-withdrawal-action_date_time', '{{%withdrawal}}');
        
        // Drop table
        $this->dropTable('{{%withdrawal}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fund_request}}`.
 */
class m251017_092305_create_fund_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fund_request}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull()->comment('Customer ID'),
            'amount' => $this->decimal(15, 2)->notNull()->comment('Requested amount'),
            'request_date' => $this->date()->notNull()->comment('Request date'),
            'attachment_file' => $this->string(255)->comment('Attachment file path'),
            'comment' => $this->text()->comment('Request comment'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('Status: 0=Pending, 1=Approved, 2=Rejected'),
            'admin_comment' => $this->text()->comment('Admin comment'),
            'processed_by' => $this->integer()->comment('Admin who processed the request'),
            'processed_at' => $this->integer()->comment('Processing timestamp'),
            'created_at' => $this->integer()->notNull()->comment('Created timestamp'),
            'updated_at' => $this->integer()->notNull()->comment('Updated timestamp'),
        ]);

        // Add foreign key constraint to customer table
        $this->addForeignKey(
            'fk-fund_request-customer_id',
            '{{%fund_request}}',
            'customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        // Add foreign key constraint to user table for processed_by
        $this->addForeignKey(
            'fk-fund_request-processed_by',
            '{{%fund_request}}',
            'processed_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Create indexes for better performance
        $this->createIndex('idx-fund_request-customer_id', '{{%fund_request}}', 'customer_id');
        $this->createIndex('idx-fund_request-status', '{{%fund_request}}', 'status');
        $this->createIndex('idx-fund_request-request_date', '{{%fund_request}}', 'request_date');
        $this->createIndex('idx-fund_request-processed_by', '{{%fund_request}}', 'processed_by');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-fund_request-customer_id', '{{%fund_request}}');
        $this->dropForeignKey('fk-fund_request-processed_by', '{{%fund_request}}');
        
        // Drop indexes
        $this->dropIndex('idx-fund_request-customer_id', '{{%fund_request}}');
        $this->dropIndex('idx-fund_request-status', '{{%fund_request}}');
        $this->dropIndex('idx-fund_request-request_date', '{{%fund_request}}');
        $this->dropIndex('idx-fund_request-processed_by', '{{%fund_request}}');
        
        // Drop table
        $this->dropTable('{{%fund_request}}');
    }
}

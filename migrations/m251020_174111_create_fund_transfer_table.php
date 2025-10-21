<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fund_transfer}}`.
 */
class m251020_174111_create_fund_transfer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fund_transfer}}', [
            'id' => $this->primaryKey(),
            'from_customer_id' => $this->integer()->notNull()->comment('Customer sending the funds'),
            'to_customer_id' => $this->integer()->notNull()->comment('Customer receiving the funds'),
            'amount' => $this->decimal(10, 2)->notNull()->comment('Transfer amount'),
            'transfer_date' => $this->date()->notNull()->comment('Date of transfer'),
            'status' => $this->integer()->notNull()->defaultValue(0)->comment('Transfer status: 0=pending, 1=approved, 2=rejected'),
            'transfer_type' => $this->integer()->notNull()->defaultValue(1)->comment('Transfer type: 1=customer_to_customer, 2=admin_transfer'),
            'comment' => $this->text()->comment('Transfer comment/description'),
            'admin_comment' => $this->text()->comment('Admin comment for approval/rejection'),
            'processed_by' => $this->integer()->comment('Admin who processed the transfer'),
            'processed_at' => $this->integer()->comment('Timestamp when processed'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Add foreign key constraints
        $this->addForeignKey(
            'fk-fund_transfer-from_customer_id',
            '{{%fund_transfer}}',
            'from_customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-fund_transfer-to_customer_id',
            '{{%fund_transfer}}',
            'to_customer_id',
            '{{%customer}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-fund_transfer-processed_by',
            '{{%fund_transfer}}',
            'processed_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );

        // Add indexes for better performance
        $this->createIndex(
            'idx-fund_transfer-from_customer_id',
            '{{%fund_transfer}}',
            'from_customer_id'
        );

        $this->createIndex(
            'idx-fund_transfer-to_customer_id',
            '{{%fund_transfer}}',
            'to_customer_id'
        );

        $this->createIndex(
            'idx-fund_transfer-status',
            '{{%fund_transfer}}',
            'status'
        );

        $this->createIndex(
            'idx-fund_transfer-transfer_type',
            '{{%fund_transfer}}',
            'transfer_type'
        );

        $this->createIndex(
            'idx-fund_transfer-transfer_date',
            '{{%fund_transfer}}',
            'transfer_date'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys
        $this->dropForeignKey('fk-fund_transfer-from_customer_id', '{{%fund_transfer}}');
        $this->dropForeignKey('fk-fund_transfer-to_customer_id', '{{%fund_transfer}}');
        $this->dropForeignKey('fk-fund_transfer-processed_by', '{{%fund_transfer}}');

        // Drop indexes
        $this->dropIndex('idx-fund_transfer-from_customer_id', '{{%fund_transfer}}');
        $this->dropIndex('idx-fund_transfer-to_customer_id', '{{%fund_transfer}}');
        $this->dropIndex('idx-fund_transfer-status', '{{%fund_transfer}}');
        $this->dropIndex('idx-fund_transfer-transfer_type', '{{%fund_transfer}}');
        $this->dropIndex('idx-fund_transfer-transfer_date', '{{%fund_transfer}}');

        // Drop table
        $this->dropTable('{{%fund_transfer}}');
    }
}
<?php

use yii\db\Migration;

class m251021_175925_add_kyc_fields_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add KYC fields to customer table
        $this->addColumn('{{%customer}}', 'crypto_wallet_address', $this->string(255)->null()->comment('Crypto wallet address for withdrawals'));
        $this->addColumn('{{%customer}}', 'upi_id', $this->string(100)->null()->comment('UPI ID for payments'));
        $this->addColumn('{{%customer}}', 'qr_code_image', $this->string(255)->null()->comment('QR code image path'));
        $this->addColumn('{{%customer}}', 'kyc_status', $this->integer()->notNull()->defaultValue(0)->comment('KYC status: 0=Pending, 1=Verified, 2=Rejected'));
        $this->addColumn('{{%customer}}', 'kyc_verified_at', $this->timestamp()->null()->comment('KYC verification date'));
        $this->addColumn('{{%customer}}', 'kyc_verified_by', $this->integer()->null()->comment('User ID who verified KYC'));
        
        // Add indexes for better performance
        $this->createIndex('idx-customer-kyc-status', '{{%customer}}', 'kyc_status');
        $this->createIndex('idx-customer-upi-id', '{{%customer}}', 'upi_id');
        
        // Add foreign key for kyc_verified_by
        $this->addForeignKey(
            'fk-customer-kyc-verified-by',
            '{{%customer}}',
            'kyc_verified_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove foreign key
        $this->dropForeignKey('fk-customer-kyc-verified-by', '{{%customer}}');
        
        // Remove indexes
        $this->dropIndex('idx-customer-kyc-status', '{{%customer}}');
        $this->dropIndex('idx-customer-upi-id', '{{%customer}}');
        
        // Remove KYC columns
        $this->dropColumn('{{%customer}}', 'crypto_wallet_address');
        $this->dropColumn('{{%customer}}', 'upi_id');
        $this->dropColumn('{{%customer}}', 'qr_code_image');
        $this->dropColumn('{{%customer}}', 'kyc_status');
        $this->dropColumn('{{%customer}}', 'kyc_verified_at');
        $this->dropColumn('{{%customer}}', 'kyc_verified_by');
    }
}